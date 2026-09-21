<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\ModuleAccess;
use App\Models\ModulePayment;
use App\Models\ModulePrice;
use App\Models\User;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_platform_admin' => true]);
    }

    private function negocio(): array
    {
        $owner = User::factory()->create();
        $business = Business::createForAccount($owner, 'Barbería Patagonia');

        return [$owner, $business];
    }

    private function activar(Business $business, Module $module): ModuleAccess
    {
        return ModuleAccess::ofBusiness($business)->where('module', $module->value)->first()->activate();
    }

    public function test_definir_precio_mensual_por_modulo(): void
    {
        $admin = $this->admin();
        $business = $this->negocio()[1];

        $this->actingAs($admin)->get(route('admin.billing.prices.index'))
            ->assertOk()
            ->assertSee('Perfil Digital');

        $this->actingAs($admin)->post(route('admin.billing.prices.update'), [
            'prices' => ['catalog' => 4990, 'reservations' => 7990, 'clients' => 5990, 'communications' => 2990],
        ])->assertSessionHas('status');

        $this->assertEquals(4990, app(BillingService::class)->priceFor(Module::Catalog));
        $this->assertNull(app(BillingService::class)->priceFor(Module::Profile));
    }

    public function test_se_puede_definir_el_precio_del_kit(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.billing.prices.update'), [
            'prices' => ['catalog' => 4990, 'reservations' => 7990, 'clients' => 5990, 'communications' => 2990],
            'kit_price' => 14990,
        ])->assertSessionHas('status');

        $this->assertEquals(14990, \App\Models\Setting::getInt(\App\Models\Setting::KIT_PRICE));

        $this->actingAs($admin)->get(route('admin.billing.prices.index'))
            ->assertOk()
            ->assertSee('Kit físico');
    }

    public function test_sin_precio_no_se_aplica_regla_de_impago(): void
    {
        [, $business] = $this->negocio();
        $this->activar($business, Module::Catalog);

        $this->assertEquals('al_dia', app(BillingService::class)->statusFor($business, Module::Catalog));
    }

    public function test_gracia_inicial_mantiene_al_dia_sin_pago(): void
    {
        [, $business] = $this->negocio();
        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);
        $this->activar($business, Module::Reservations);

        $this->assertEquals('al_dia', app(BillingService::class)->statusFor($business, Module::Reservations));
    }

    public function test_vencida_la_gracia_sin_pago_queda_atrasado(): void
    {
        [, $business] = $this->negocio();
        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);
        $access = $this->activar($business, Module::Reservations);
        $access->update(['activated_at' => Carbon::today()->subDays(15)]);

        $this->assertEquals('atrasado', app(BillingService::class)->statusFor($business, Module::Reservations));
    }

    public function test_un_pago_vigente_deja_al_dia_por_30_dias(): void
    {
        [, $business] = $this->negocio();
        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);
        $access = $this->activar($business, Module::Reservations);
        $access->update(['activated_at' => Carbon::today()->subDays(15)]);

        app(BillingService::class)->registerPayment($business, Module::Reservations, 7990, today()->toDateString());

        $this->assertEquals('al_dia', app(BillingService::class)->statusFor($business, Module::Reservations));

        ModulePayment::where('business_id', $business->id)->update(['paid_on' => Carbon::today()->subDays(40)]);

        $this->assertEquals('atrasado', app(BillingService::class)->statusFor($business, Module::Reservations));
    }

    public function test_apply_billing_apaga_modulos_atrasados_y_conserva_datos(): void
    {
        [, $business] = $this->negocio();
        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);
        $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);

        $access = $this->activar($business, Module::Reservations);
        $access->update(['activated_at' => Carbon::today()->subDays(20)]);

        $this->assertEquals('atrasado', app(BillingService::class)->statusFor($business, Module::Reservations));

        $count = app(BillingService::class)->applyBilling();

        $this->assertEquals(1, $count);
        $this->assertFalse($business->fresh()->isModuleActive(Module::Reservations));
        $this->assertEquals(1, $business->bookingServices()->count(), 'los datos se conservan al apagar por impago');
        $this->assertTrue($business->fresh()->isModuleActive(Module::Profile), 'el perfil gratuito nunca se apaga');
    }

    public function test_registrar_pago_reactiva_un_modulo_apagado_por_impago(): void
    {
        [, $business] = $this->negocio();
        ModulePrice::create(['module' => 'catalog', 'price_monthly' => 4990]);
        $business->catalogItems()->create(['name' => 'Corte + barba', 'price_mode' => 'exact', 'price' => 18000, 'active' => true]);

        $access = $this->activar($business, Module::Catalog);
        $access->update(['activated_at' => Carbon::today()->subDays(20)]);

        app(BillingService::class)->applyBilling();
        $this->assertFalse($business->fresh()->isModuleActive(Module::Catalog));

        app(BillingService::class)->registerPayment($business, Module::Catalog, 4990, today()->toDateString());

        $this->assertTrue($business->fresh()->isModuleActive(Module::Catalog));
        $this->assertEquals('al_dia', app(BillingService::class)->statusFor($business, Module::Catalog));
    }

    public function test_seccion_pagos_muestra_estados_y_registra_pagos(): void
    {
        $admin = $this->admin();
        [, $business] = $this->negocio();
        ModulePrice::create(['module' => 'catalog', 'price_monthly' => 4990]);

        $this->actingAs($admin)->get(route('admin.billing.payments.index'))
            ->assertOk()
            ->assertSee($business->name);

        $this->actingAs($admin)->post(route('admin.billing.payments.store', $business), [
            'module' => 'catalog',
            'amount' => 4990,
            'paid_on' => today()->toDateString(),
            'notes' => 'Transferencia',
        ])->assertSessionHas('status');

        $this->assertEquals(1, ModulePayment::count());
        $this->assertEquals('Transferencia', ModulePayment::first()->notes);
    }

    public function test_el_perfil_gratuito_no_tiene_precio_ni_se_evalua(): void
    {
        $admin = $this->admin();
        [, $business] = $this->negocio();

        ModulePrice::create(['module' => 'reservations', 'price_monthly' => 7990]);
        $access = $this->activar($business, Module::Reservations);
        $access->update(['activated_at' => Carbon::today()->subDays(30)]);

        $this->assertEquals('al_dia', app(BillingService::class)->statusFor($business, Module::Profile));

        $this->actingAs($admin)->get(route('admin.billing.prices.index'))
            ->assertSee('Gratis');
    }
}
