<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_visitas_y_escaneos_se_registran_por_negocio(): void
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');

        $this->get(route('p.show', $business->slug));
        $this->get(route('p.show', $business->slug));

        $counts = app(AnalyticsService::class)->counts($business);

        $this->assertEquals(2, $counts['visits']);
    }

    public function test_los_clicks_de_contacto_y_redes_se_registran(): void
    {
        [, $business] = $this->negocio();

        $this->get(route('p.click', [$business->slug, 'whatsapp']))->assertStatus(204);
        $this->get(route('p.click', [$business->slug, 'instagram']))->assertStatus(204);
        $this->get(route('p.click', [$business->slug, 'llamar']))->assertStatus(204);

        $counts = app(AnalyticsService::class)->counts($business);

        $this->assertEquals(3, $counts['clicks']);
        $this->get(route('p.click', [$business->slug, 'desconocido']))->assertNotFound();
    }

    public function test_el_panel_muestra_las_estadisticas_del_negocio(): void
    {
        [$user, $business] = $this->negocio();
        $business->clients()->create(['name' => 'Cliente X']);

        $this->actingAs($user)->get(route('panel.index'))
            ->assertOk()
            ->assertSee('Visitas del perfil')
            ->assertSee('Escaneos QR')
            ->assertSee('Clientes');
    }

    public function test_las_estadisticas_no_mezclan_negocios(): void
    {
        [, $negocioA] = $this->negocio();
        [, $negocioB] = $this->negocio();

        $this->get(route('p.show', $negocioA->slug));

        $this->assertEquals(1, app(AnalyticsService::class)->counts($negocioA)['visits']);
        $this->assertEquals(0, app(AnalyticsService::class)->counts($negocioB)['visits']);
    }

    private function negocio(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Negocio '.fake()->word());

        return [$user, $business];
    }
}
