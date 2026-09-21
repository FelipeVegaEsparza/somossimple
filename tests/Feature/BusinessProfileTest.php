<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BusinessProfileTest extends TestCase
{
    use RefreshDatabase;

    private function negocioConCuenta(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');

        return [$user, $business];
    }

    public function test_el_propietario_actualiza_la_informacion_del_perfil(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)
            ->put(route('panel.profile.update'), [
                'name' => 'Barbería Patagonia',
                'description' => 'Barbería masculina en Chile Chico.',
                'phone' => '+56 9 1234 5678',
                'whatsapp' => '+56 9 1234 5678',
                'contact_email' => 'hola@barberia.cl',
                'website' => 'https://barberia.cl',
                'address' => 'Av. O Higgins 123, Chile Chico',
                'map_url' => 'https://maps.google.com/?q=chile+chico',
                'opening_hours' => 'Mar a Sáb, 10:00 a 20:00',
            ])
            ->assertRedirect(route('panel.profile.edit'));

        $business->refresh();

        $this->assertEquals('Barbería masculina en Chile Chico.', $business->description);
        $this->assertEquals('+56 9 1234 5678', $business->whatsapp);
        $this->assertEquals('Mar a Sáb, 10:00 a 20:00', $business->opening_hours);
    }

    public function test_agregar_y_eliminar_una_red_social(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'networks' => ['instagram' => 'https://instagram.com/barberia'],
        ]);

        $this->assertDatabaseHas('profile_links', [
            'business_id' => $business->id,
            'kind' => 'social',
            'network' => 'instagram',
        ]);

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'networks' => [],
        ]);

        $this->assertDatabaseMissing('profile_links', [
            'business_id' => $business->id,
            'kind' => 'social',
            'network' => 'instagram',
        ]);
    }

    public function test_crear_y_eliminar_botones_personalizados(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'buttons_label' => ['Solicitar presupuesto'],
            'buttons_url' => ['https://barberia.cl/presupuesto'],
        ]);

        $this->assertDatabaseHas('profile_links', [
            'business_id' => $business->id,
            'kind' => 'button',
            'label' => 'Solicitar presupuesto',
        ]);

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'buttons_label' => [],
            'buttons_url' => [],
        ]);

        $this->assertEquals(0, $business->customButtons()->count());
    }

    public function test_la_url_publica_es_unica_y_estable_ante_cambios_de_contenido(): void
    {
        [$user, $business] = $this->negocioConCuenta();
        $otra = User::factory()->create();
        Business::createForAccount($otra, 'Barbería Patagonia');

        $this->assertEquals(2, Business::where('slug', 'like', 'barberia-patagonia%')->count());

        $slug = $business->slug;

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => 'Barbería Patagonia Austral',
            'phone' => '+56 9 0000 0000',
            'description' => 'Cambiamos los datos.',
        ]);

        $this->assertEquals($slug, $business->fresh()->slug);

        $this->get(route('p.show', $slug))->assertOk();
        $this->get(route('p.show', $slug))->assertSee('Cambiamos los datos.');
    }

    public function test_el_perfil_publico_muestra_segun_modulos_activos(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'description' => 'Barbería masculina.',
            'phone' => '+56 9 1111 1111',
            'whatsapp' => '+56 9 1111 1111',
            'map_url' => 'https://maps.google.com/?q=chile+chico',
            'networks' => ['instagram' => 'https://instagram.com/barberia'],
        ]);

        // Solo perfil gratuito: sin acceso a reservas.
        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee('Barbería Patagonia')
            ->assertSee('WhatsApp')
            ->assertSee('Instagram')
            ->assertDontSee('Agendar hora')
            ->assertDontSee(route('p.reservation', $business->slug), false);

        // Con reservas activas: aparece la card de Reservas (sin botón en el perfil).
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);

        $this->get(route('p.show', $business->slug))
            ->assertSee(route('p.reservation', $business->slug), false)
            ->assertDontSee('Agendar hora');

        // Al desactivar reservas, el acceso desaparece del perfil público.
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => false]);

        $this->get(route('p.show', $business->slug))
            ->assertDontSee(route('p.reservation', $business->slug), false);
    }

    public function test_la_galeria_puede_guardar_varias_imagenes(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'gallery' => [
                UploadedFile::fake()->image('foto1.jpg', 10, 10),
                UploadedFile::fake()->image('foto2.jpg', 10, 10),
                UploadedFile::fake()->image('foto3.jpg', 10, 10),
            ],
        ]);

        $this->assertEquals(3, $business->gallery()->count());
    }

    public function test_negocio_inexistente_devuelve_404(): void
    {
        $this->get(route('p.show', 'no-existe'))->assertNotFound();
    }

    public function test_el_negocio_elige_el_diseno_de_su_perfil_publico(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)->get(route('panel.profile.edit'))
            ->assertOk()
            ->assertSee('Diseño del perfil')
            ->assertSee('Clásico')
            ->assertSee('Nocturno')
            ->assertSee('Cálido')
            ->assertSee('Minimal')
            ->assertSee('Esmeralda')
            ->assertSee('Violeta')
            ->assertSee('Océano')
            ->assertSee('Elegante');

        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'theme' => 'nocturno',
        ])->assertRedirect(route('panel.profile.edit'));

        $this->assertEquals('nocturno', $business->fresh()->theme);
        $this->get(route('p.show', $business->slug))->assertSee('theme-nocturno', false);

        // Un tema inválido no se guarda.
        $this->actingAs($user)->put(route('panel.profile.update'), [
            'name' => $business->name,
            'theme' => 'inventado',
        ])->assertSessionHasErrors('theme');

        $this->assertEquals('nocturno', $business->fresh()->theme);
    }

    public function test_los_temas_cambian_la_estructura_del_perfil(): void
    {
        [, $business] = $this->negocioConCuenta();

        foreach (['clasico' => 'cover', 'nocturno' => 'overlay', 'calido' => 'hero', 'minimal' => 'central'] as $theme => $layout) {
            $business->update(['theme' => $theme]);

            $this->get(route('p.show', $business->slug))
                ->assertOk()
                ->assertSee('data-layout="'.$layout.'"', false)
                ->assertSee('theme-'.$theme, false);
        }
    }

    public function test_guardar_perfil_funciona_aunque_haya_galeria(): void
    {
        [$user, $business] = $this->negocioConCuenta();
        $image = $business->gallery()->create(['path' => 'perfil/foto.jpg', 'position' => 0]);

        $html = $this->actingAs($user)->get(route('panel.profile.edit'))->assertOk()->getContent();

        // El botón de guardar debe quedar dentro del formulario principal (sin formularios anidados).
        $formStart = strpos($html, 'action="'.route('panel.profile.update').'"');
        $formEnd = strpos($html, '</form>', $formStart);
        $buttonPos = strpos($html, 'Guardar perfil');

        $this->assertNotFalse($formStart);
        $this->assertGreaterThan($formStart, $buttonPos, 'El botón Guardar perfil quedó antes del formulario principal.');
        $this->assertLessThan($formEnd, $buttonPos, 'El botón Guardar perfil quedó fuera del formulario principal.');
        $this->assertStringContainsString('form="delete-gallery-'.$image->id.'"', $html);

        // El formulario externo de eliminación sigue funcionando.
        $this->actingAs($user)->delete(route('panel.profile.gallery.destroy', $image))->assertRedirect();
        $this->assertEquals(0, $business->gallery()->count());
    }
}
