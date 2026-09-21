<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    private function negocio(): Business
    {
        $user = User::factory()->create();

        return Business::createForAccount($user, 'Barbería Patagonia');
    }

    public function test_el_manifiesto_es_instalable(): void
    {
        $business = $this->negocio();

        $response = $this->get(route('p.manifest', $business->slug));

        $response->assertOk();
        $this->assertStringContainsString('application/manifest+json', $response->headers->get('content-type'));
        $response->assertJsonPath('name', 'Barbería Patagonia');
        $response->assertJsonPath('start_url', '/'.$business->slug.'?source=pwa');
        $response->assertJsonPath('scope', '/'.$business->slug);
        $this->assertCount(3, $response->json('icons'));
    }

    public function test_el_service_worker_tiene_el_scope_del_negocio(): void
    {
        $business = $this->negocio();

        $response = $this->get(route('p.sw', $business->slug));

        $response->assertOk();
        $this->assertEquals('/'.$business->slug, $response->headers->get('service-worker-allowed'));
        $this->assertStringContainsString('application/javascript', $response->headers->get('content-type'));
        $this->assertStringContainsString('somossimple-'.$business->id, $response->getContent());
    }

    public function test_los_iconos_se_generan_en_png(): void
    {
        $business = $this->negocio();

        foreach ([180, 192, 512] as $size) {
            $response = $this->get(route('p.icon', [$business->slug, $size]));

            $response->assertOk();
            $this->assertStringContainsString('image/png', $response->headers->get('content-type'));
            $this->assertSame("\x89PNG", substr($response->getContent(), 0, 4));
        }

        $this->get(route('p.icon', [$business->slug, 100]))->assertNotFound();
    }

    public function test_el_perfil_enlaza_la_pwa(): void
    {
        $business = $this->negocio();

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee(route('p.manifest', $business->slug), false)
            ->assertSee('sw.js', false);
    }

    public function test_una_cuenta_pausada_no_expone_la_pwa(): void
    {
        $business = $this->negocio();
        $business->update(['is_paused' => true]);

        $this->get(route('p.manifest', $business->slug))->assertNotFound();
        $this->get(route('p.sw', $business->slug))->assertNotFound();
        $this->get(route('p.icon', [$business->slug, 192]))->assertNotFound();
    }

    public function test_el_perfil_ofrece_el_boton_instalar_app(): void
    {
        $business = $this->negocio();

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee('Instalar APP')
            ->assertSee('beforeinstallprompt', false)
            ->assertSee('Añadir a pantalla de inicio');
    }
}
