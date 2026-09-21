<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Livewire\ModulesManager;
use App\Models\Business;
use App\Models\ModuleActivationRequest;
use App\Models\ModuleAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function negocioConCuenta(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');

        return [$user, $business];
    }

    private function admin(): User
    {
        return User::factory()->create(['is_platform_admin' => true]);
    }

    public function test_estado_inicial_de_un_negocio_nuevo(): void
    {
        [, $business] = $this->negocioConCuenta();

        $this->assertTrue($business->isModuleActive(Module::Profile));
        $this->assertFalse($business->isModuleActive(Module::Catalog));
        $this->assertFalse($business->isModuleActive(Module::Reservations));
        $this->assertFalse($business->isModuleActive(Module::Clients));
        $this->assertFalse($business->isModuleActive(Module::Communications));
    }

    public function test_el_dueño_consulta_los_modulos_desde_su_panel(): void
    {
        [$user] = $this->negocioConCuenta();

        $this->actingAs($user)->get(route('panel.modules'))
            ->assertOk()
            ->assertSee('Perfil Digital')
            ->assertSee('Catálogo');
    }

    public function test_el_menu_lateral_solo_muestra_los_modulos_activos(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        $this->actingAs($user)->get(route('panel.kit.index'))
            ->assertSee('Módulos')
            ->assertDontSee('Catálogo')
            ->assertDontSee('Reservas')
            ->assertDontSee('Clientes')
            ->assertDontSee('Comunicaciones');

        $business->moduleAccess()->where('module', Module::Catalog->value)->first()->activate();
        $business->moduleAccess()->where('module', Module::Reservations->value)->first()->activate();

        $this->actingAs($user->fresh())->get(route('panel.kit.index'))
            ->assertSee('Catálogo')
            ->assertSee('Reservas')
            ->assertDontSee('Clientes');
    }

    public function test_el_dueño_solicita_activar_un_modulo_y_este_no_se_activa_solo(): void
    {
        [$user, $business] = $this->negocioConCuenta();

        Livewire::actingAs($user)
            ->test(ModulesManager::class)
            ->call('solicitar', Module::Reservations->value, 'activate')
            ->assertSet('notice', 'Solicitud enviada para activar el módulo Reservas. Te contactaremos.');

        $this->assertEquals(1, ModuleActivationRequest::where('business_id', $business->id)->where('module', 'reservations')->count());
        $this->assertFalse($business->fresh()->isModuleActive(Module::Reservations), 'el dueño no activa módulos de pago por sí mismo');
    }

    public function test_no_se_envia_una_segunda_solicitud_pendiente(): void
    {
        [$user] = $this->negocioConCuenta();
        $business = $user->business;

        ModuleActivationRequest::create(['business_id' => $business->id, 'module' => 'catalog', 'action' => 'activate', 'status' => 'pending']);

        Livewire::actingAs($user)
            ->test(ModulesManager::class)
            ->call('solicitar', Module::Catalog->value, 'activate')
            ->assertSet('notice', 'Ya hay una solicitud pendiente para el módulo Catálogo.');

        $this->assertEquals(1, ModuleActivationRequest::count());
    }

    public function test_el_perfil_gratuito_no_genera_solicitud(): void
    {
        [$user] = $this->negocioConCuenta();

        Livewire::actingAs($user)
            ->test(ModulesManager::class)
            ->call('solicitar', Module::Profile->value, 'activate')
            ->assertSet('notice', 'El Perfil Digital es gratuito y siempre está activo.');

        $this->assertEquals(0, ModuleActivationRequest::count());
        $this->assertTrue($user->business->fresh()->isModuleActive(Module::Profile));
    }

    public function test_el_admin_aprueba_una_solicitud_de_activacion(): void
    {
        $admin = $this->admin();
        [$user, $business] = $this->negocioConCuenta();
        $solicitud = ModuleActivationRequest::create(['business_id' => $business->id, 'module' => 'catalog', 'action' => 'activate', 'status' => 'pending']);

        $this->actingAs($admin)->post(route('admin.modulerequests.approve', $solicitud))
            ->assertSessionHas('status');

        $this->assertTrue($business->fresh()->isModuleActive(Module::Catalog));
        $this->assertEquals(0, ModuleActivationRequest::count());
    }

    public function test_el_dueño_solicita_desactivar_un_modulo_activo(): void
    {
        $admin = $this->admin();
        [$user, $business] = $this->negocioConCuenta();
        $business->moduleAccess()->where('module', Module::Reservations->value)->first()->activate();

        Livewire::actingAs($user)
            ->test(ModulesManager::class)
            ->call('solicitar', Module::Reservations->value, 'deactivate')
            ->assertSet('notice', 'Solicitud enviada para desactivar el módulo Reservas. Te contactaremos.');

        $this->assertTrue($business->fresh()->isModuleActive(Module::Reservations), 'sigue activo hasta que el admin apruebe');

        $solicitud = ModuleActivationRequest::first();
        $this->actingAs($admin)->post(route('admin.modulerequests.approve', $solicitud));

        $this->assertFalse($business->fresh()->isModuleActive(Module::Reservations));
    }

    public function test_desactivar_conserva_datos_y_se_puede_reactivar(): void
    {
        $admin = $this->admin();
        [$user, $business] = $this->negocioConCuenta();
        $business->catalogItems()->create(['name' => 'Corte + barba', 'price_mode' => 'exact', 'price' => 18000, 'active' => true]);

        // El admin activa directamente desde su ficha.
        $access = ModuleAccess::ofBusiness($business)->where('module', Module::Catalog->value)->first();
        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Catalog->value]));
        $this->assertTrue($business->fresh()->isModuleActive(Module::Catalog));

        // Desactiva conservando datos.
        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Catalog->value]));
        $this->assertFalse($business->fresh()->isModuleActive(Module::Catalog));
        $this->assertEquals(1, $business->catalogItems()->count(), 'los datos no se eliminan al desactivar');

        // Se reactiva vía solicitud aprobada y la información continúa disponible.
        $solicitud = ModuleActivationRequest::create(['business_id' => $business->id, 'module' => 'catalog', 'action' => 'activate', 'status' => 'pending']);
        $this->actingAs($admin)->post(route('admin.modulerequests.approve', $solicitud));

        $this->assertTrue($business->fresh()->isModuleActive(Module::Catalog));
        $this->assertEquals(1, $business->catalogItems()->count());
    }
}
