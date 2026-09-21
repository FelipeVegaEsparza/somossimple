<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\Event;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SolutionModulesTest extends TestCase
{
    use RefreshDatabase;

    private function negocioConModulo(Module $module): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Negocio Simple');
        $business->moduleAccess()->where('module', $module->value)->update(['active' => true]);

        return [$user, $business];
    }

    public function test_los_modulos_de_la_landing_aparecen_en_el_panel_del_negocio(): void
    {
        [$user] = $this->negocioConModulo(Module::Catalog);

        $this->actingAs($user)->get(route('panel.modules'))
            ->assertOk()
            ->assertSee('Menú')
            ->assertSee('Servicios')
            ->assertSee('Promociones')
            ->assertSee('Eventos')
            ->assertSee('Galería');
    }

    public function test_el_admin_puede_activar_los_nuevos_modulos(): void
    {
        $admin = User::factory()->create(['is_platform_admin' => true]);
        [, $business] = $this->negocioConModulo(Module::Catalog);

        $this->actingAs($admin)->get(route('admin.business.show', $business))
            ->assertOk()
            ->assertSee('Menú')
            ->assertSee('Servicios')
            ->assertSee('Promociones')
            ->assertSee('Eventos')
            ->assertSee('Galería');

        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Menu->value]));
        $this->assertTrue($business->fresh()->isModuleActive(Module::Menu));
    }

    public function test_menu_se_crea_y_se_muestra_en_el_perfil(): void
    {
        [$user, $business] = $this->negocioConModulo(Module::Menu);
        $entradas = $business->menuCategories()->create(['name' => 'Entradas']);

        $this->actingAs($user)->get(route('panel.menu.index'))->assertOk();
        $this->actingAs($user)->get(route('panel.menu.create'))->assertOk();

        $this->actingAs($user)->post(route('panel.menu.store'), [
            'name' => 'Empanadas de mariscos',
            'description' => 'Tres unidades.',
            'category_id' => $entradas->id,
            'price' => 5990,
            'featured' => '1',
            'active' => '1',
        ])->assertRedirect(route('panel.menu.index'));

        MenuItem::create([
            'business_id' => $business->id,
            'category_id' => $entradas->id,
            'name' => 'Sopa de mariscos',
            'price' => 6990,
            'active' => true,
        ]);

        $html = $this->get(route('p.menu', $business->slug))
            ->assertOk()
            ->assertSee('Menú')
            ->assertSee('Destacados')
            ->assertSee('Entradas')
            ->assertSee('Empanadas de mariscos')
            ->assertSee('Sopa de mariscos')
            ->assertSee('$5.990')
            ->getContent();

        $this->assertEquals(1, substr_count($html, 'Empanadas de mariscos'), 'un destacado no se duplica dentro de su categoría');

        $item = MenuItem::ofBusiness($business)->where('name', 'Empanadas de mariscos')->first();
        $this->assertTrue($item->featured);

        $business->moduleAccess()->where('module', Module::Menu->value)->update(['active' => false]);
        $this->get(route('p.menu', $business->slug))->assertNotFound();
        $this->assertEquals(2, $business->menuItems()->count(), 'los datos no se eliminan al desactivar el módulo');
    }

    public function test_servicios_se_crean_y_se_muestran_con_duracion_y_precio(): void
    {
        [$user, $business] = $this->negocioConModulo(Module::Services);

        $this->actingAs($user)->get(route('panel.services.index'))->assertOk();
        $this->actingAs($user)->get(route('panel.services.create'))->assertOk();

        $this->actingAs($user)->post(route('panel.services.store'), [
            'name' => 'Corte clásico',
            'description' => 'Corte con tijera y máquina.',
            'price' => 12000,
            'duration_minutes' => 45,
            'active' => '1',
        ])->assertRedirect(route('panel.services.index'));

        $service = Service::ofBusiness($business)->first();
        $this->assertEquals(45, $service->duration_minutes);

        $this->get(route('p.services', $business->slug))
            ->assertSee('Servicios')
            ->assertSee('Corte clásico')
            ->assertSee('$12.000')
            ->assertSee('45 min');

        $business->moduleAccess()->where('module', Module::Services->value)->update(['active' => false]);
        $this->get(route('p.services', $business->slug))->assertNotFound();
    }

    public function test_promociones_solo_muestran_las_vigentes(): void
    {
        [$user, $business] = $this->negocioConModulo(Module::Promotions);

        $this->actingAs($user)->get(route('panel.promotions.index'))->assertOk();
        $this->actingAs($user)->get(route('panel.promotions.create'))->assertOk();

        $this->actingAs($user)->post(route('panel.promotions.store'), [
            'title' => '2x1 los martes',
            'description' => 'Válido solo los martes.',
            'starts_on' => now()->subDay()->toDateString(),
            'ends_on' => now()->addWeek()->toDateString(),
            'active' => '1',
        ])->assertRedirect(route('panel.promotions.index'));

        Promotion::create([
            'business_id' => $business->id,
            'title' => 'Oferta vencida',
            'active' => true,
            'ends_on' => now()->subMonth()->toDateString(),
        ]);

        $this->get(route('p.promotions', $business->slug))
            ->assertSee('Promociones')
            ->assertSee('2x1 los martes')
            ->assertDontSee('Oferta vencida');

        $business->moduleAccess()->where('module', Module::Promotions->value)->update(['active' => false]);
        $this->get(route('p.promotions', $business->slug))->assertNotFound();
    }

    public function test_eventos_solo_muestran_los_proximos(): void
    {
        [$user, $business] = $this->negocioConModulo(Module::Events);

        $this->actingAs($user)->get(route('panel.events.index'))->assertOk();
        $this->actingAs($user)->get(route('panel.events.create'))->assertOk();

        $this->actingAs($user)->post(route('panel.events.store'), [
            'title' => 'Taller de fotografía',
            'description' => 'Cupos limitados.',
            'location' => 'Av. Principal 123',
            'starts_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'active' => '1',
        ])->assertRedirect(route('panel.events.index'));

        Event::create([
            'business_id' => $business->id,
            'title' => 'Evento pasado',
            'starts_at' => now()->subDay(),
            'active' => true,
        ]);

        $this->get(route('p.events', $business->slug))
            ->assertSee('Eventos')
            ->assertSee('Taller de fotografía')
            ->assertSee('Av. Principal 123')
            ->assertDontSee('Evento pasado');

        $business->moduleAccess()->where('module', Module::Events->value)->update(['active' => false]);
        $this->get(route('p.events', $business->slug))->assertNotFound();
    }

    public function test_galeria_se_administra_en_su_modulo_y_se_muestra_en_el_perfil(): void
    {
        [$user, $business] = $this->negocioConModulo(Module::Gallery);

        $this->actingAs($user)->get(route('panel.gallery.index'))->assertOk();

        $this->actingAs($user)->post(route('panel.gallery.store'), [
            'images' => [
                UploadedFile::fake()->image('local1.jpg', 10, 10),
                UploadedFile::fake()->image('local2.jpg', 10, 10),
            ],
        ])->assertRedirect();

        $this->assertEquals(2, $business->gallery()->count());

        $path = $business->gallery()->first()->path;
        $this->get(route('p.gallery', $business->slug))
            ->assertSee('Galería')
            ->assertSee($path);

        $business->moduleAccess()->where('module', Module::Gallery->value)->update(['active' => false]);
        $this->get(route('p.gallery', $business->slug))->assertNotFound();

        $this->assertEquals(2, $business->gallery()->count(), 'las imágenes se conservan');
    }

    public function test_el_perfil_es_el_acceso_a_los_modulos_activos(): void
    {
        [, $business] = $this->negocioConModulo(Module::Menu);
        $business->moduleAccess()->where('module', Module::Services->value)->update(['active' => true]);
        $business->menuItems()->create(['name' => 'Empanada', 'price' => 3000, 'active' => true]);
        $business->services()->create(['name' => 'Corte', 'price' => 12000, 'active' => true]);

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee('Explora')
            ->assertSee(route('p.menu', $business->slug), false)
            ->assertSee(route('p.services', $business->slug), false)
            ->assertSee('target="_blank"', false)
            ->assertDontSee(route('p.promotions', $business->slug), false);
    }

    public function test_el_perfil_enlaza_el_catalogo_cuando_esta_activo(): void
    {
        [, $business] = $this->negocioConModulo(Module::Catalog);
        $business->catalogItems()->create(['name' => 'Producto', 'price_mode' => 'exact', 'price' => 1000, 'active' => true]);

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee('Explora')
            ->assertSee(route('p.catalog', $business->slug), false);
    }

    public function test_los_modulos_activos_aparecen_aunque_no_tengan_contenido(): void
    {
        [, $business] = $this->negocioConModulo(Module::Catalog);

        foreach ([Module::Menu, Module::Services, Module::Reservations, Module::Promotions, Module::Events, Module::Gallery] as $module) {
            $business->moduleAccess()->where('module', $module->value)->update(['active' => true]);
        }

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee(route('p.catalog', $business->slug), false)
            ->assertSee(route('p.menu', $business->slug), false)
            ->assertSee(route('p.services', $business->slug), false)
            ->assertSee(route('p.reservation', $business->slug), false)
            ->assertSee(route('p.promotions', $business->slug), false)
            ->assertSee(route('p.events', $business->slug), false)
            ->assertSee(route('p.gallery', $business->slug), false);
    }

    public function test_novedades_es_la_pagina_publica_del_modulo_clientes(): void
    {
        [, $business] = $this->negocioConModulo(Module::Clients);

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee(route('p.clients', $business->slug), false)
            ->assertSee('Novedades');

        $this->get(route('p.clients', $business->slug))
            ->assertOk()
            ->assertSee('Novedades')
            ->assertSee('Registrarme');

        $business->moduleAccess()->where('module', Module::Clients->value)->update(['active' => false]);
        $this->get(route('p.clients', $business->slug))->assertNotFound();
    }
}
