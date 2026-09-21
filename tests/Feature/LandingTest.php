<?php

namespace Tests\Feature;

use App\Models\KitRequest;
use App\Models\ModulePrice;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_landing_explica_el_producto_y_ofrece_el_kit(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Tu negocio tiene necesidades')
            ->assertSee('Empieza gratis. Crece cuando lo necesites.')
            ->assertSee('¿Quieres tus accesos físicos?');
    }

    public function test_se_puede_solicitar_un_kit_desde_la_landing(): void
    {
        $this->post(route('landing.kit.request'), [
            'name' => 'Juan Pérez',
            'business_name' => 'Barbería Patagonia',
            'whatsapp' => '+56 9 1234 5678',
            'email' => 'juan@example.com',
            'quantity' => 2,
        ])->assertRedirect();

        $solicitud = KitRequest::first();
        $this->assertNotNull($solicitud);
        $this->assertEquals('Barbería Patagonia', $solicitud->business_name);
        $this->assertEquals(2, $solicitud->quantity);
        $this->assertEquals('nueva', $solicitud->status);
    }

    public function test_la_solicitud_valida_los_datos(): void
    {
        $this->post(route('landing.kit.request'), ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->assertEquals(0, KitRequest::count());
    }

    public function test_la_landing_muestra_los_precios_definidos_en_el_panel(): void
    {
        ModulePrice::create(['module' => 'catalog', 'price_monthly' => 4990]);
        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);
        ModulePrice::create(['module' => 'menu', 'price_monthly' => 5990]);
        ModulePrice::create(['module' => 'promotions', 'price_monthly' => 8990]);

        $this->get('/')
            ->assertOk()
            ->assertSee('$4.990')
            ->assertSee('$7.990')
            ->assertSee('$5.990')
            ->assertSee('$8.990')
            ->assertSee('al mes')
            ->assertSee('Gratis');
    }

    public function test_la_landing_muestra_el_precio_del_kit_si_esta_definido(): void
    {
        Setting::set(Setting::KIT_PRICE, '14990');

        $this->get('/')
            ->assertOk()
            ->assertSee('$14.990');
    }

    public function test_el_admin_ve_y_gestiona_las_solicitudes(): void
    {
        $admin = User::factory()->create(['is_platform_admin' => true]);
        $solicitud = KitRequest::create([
            'name' => 'Ana Soto',
            'business_name' => 'Cafetería Austral',
            'whatsapp' => '+56 9 9999 9999',
            'quantity' => 1,
            'status' => 'nueva',
        ]);

        $this->actingAs($admin)->get(route('admin.kitrequests.index'))
            ->assertOk()
            ->assertSee('Ana Soto')
            ->assertSee('Cafetería Austral');

        $this->actingAs($admin)->post(route('admin.kitrequests.contact', $solicitud));
        $this->assertEquals('contactada', $solicitud->fresh()->status);

        $this->actingAs($admin)->delete(route('admin.kitrequests.destroy', $solicitud));
        $this->assertEquals(0, KitRequest::count());
    }
}
