<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\ModulePayment;
use App\Models\ModulePrice;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingPanelTest extends TestCase
{
    use RefreshDatabase;

    private function negocio(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Café Patagonia');

        return [$user, $business];
    }

    public function test_mi_plan_muestra_servicios_y_costo_mensual_total(): void
    {
        [$user, $business] = $this->negocio();

        ModulePrice::create(['module' => 'catalog', 'price_monthly' => 4990]);
        ModulePrice::create(['module' => 'clients', 'price_monthly' => 9990]);

        $business->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => true]);
        $business->moduleAccess()->where('module', Module::Clients->value)->update(['active' => true]);

        $this->actingAs($user)->get(route('panel.billing'))
            ->assertOk()
            ->assertSee('Mi plan')
            ->assertSee('Costo mensual')
            ->assertSee('$14.980')
            ->assertSee('Catálogo')
            ->assertSee('Clientes')
            ->assertSee('Gratis'); // Perfil Digital
    }

    public function test_mi_plan_marca_los_pagos_pendientes(): void
    {
        [$user, $business] = $this->negocio();

        ModulePrice::create(['module' => 'catalog', 'price_monthly' => 4990]);

        $business->moduleAccess()->where('module', Module::Catalog->value)->update([
            'active' => true,
            'activated_at' => now()->subDays(30),
        ]);

        $this->actingAs($user)->get(route('panel.billing'))
            ->assertOk()
            ->assertSee('Pago pendiente')
            ->assertSee('pago pendiente', false);
    }

    public function test_mi_plan_lista_los_pagos_registrados(): void
    {
        [$user, $business] = $this->negocio();

        ModulePrice::create(['module' => 'catalog', 'price_monthly' => 4990]);

        app(BillingService::class)->registerPayment($business, Module::Catalog, 4990, now()->toDateString(), 'Pago mensual');

        $this->actingAs($user)->get(route('panel.billing'))
            ->assertOk()
            ->assertSee('Pagos registrados')
            ->assertSee('$4.990')
            ->assertSee('Pago mensual');

        $this->assertEquals(1, ModulePayment::ofBusiness($business)->count());
    }

    public function test_mi_plan_lista_modulos_disponibles_sin_activar(): void
    {
        [$user, $business] = $this->negocio();

        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);

        $this->actingAs($user)->get(route('panel.billing'))
            ->assertOk()
            ->assertSee('Disponibles para activar')
            ->assertSee('Reservas')
            ->assertSee('$7.990');
    }

    public function test_mi_plan_requiere_sesion(): void
    {
        $this->get(route('panel.billing'))->assertRedirect(route('login'));
    }
}
