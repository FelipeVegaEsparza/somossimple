<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_platform_admin' => true]);
    }

    public function test_desactivar_modulo_desde_administracion_conserva_los_datos(): void
    {
        $admin = $this->admin();
        $business = $this->negocioDe(User::factory()->create());
        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);

        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Reservations->value]));
        $business->refresh();

        $this->assertTrue($business->isModuleActive(Module::Reservations));

        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Reservations->value]));
        $business->refresh();

        $this->assertFalse($business->isModuleActive(Module::Reservations));
        $this->assertEquals(1, $business->bookingServices()->count(), 'los datos del módulo no se eliminan al desactivar');
        $this->assertEquals('Corte', $service->fresh()->name);
    }

    private function negocioDe(User $owner, string $nombre = 'Barbería Patagonia'): Business
    {
        return Business::createForAccount($owner, $nombre);
    }

    public function test_el_admin_accede_al_area_y_una_cuenta_normal_no(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $this->negocioDe($owner);

        $this->actingAs($admin)->get(route('admin.index'))->assertOk();
        $this->actingAs($owner)->get(route('admin.index'))->assertForbidden();
    }

    public function test_el_admin_al_iniciar_sesion_entra_al_dashboard_de_administracion(): void
    {
        $admin = $this->admin();
        $admin->update(['password' => bcrypt('secret123')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'secret123'])
            ->assertRedirect(route('admin.index'));
    }

    public function test_listar_y_buscar_negocios(): void
    {
        $admin = $this->admin();
        $this->negocioDe(User::factory()->create(), 'Barbería Patagonia');
        $this->negocioDe(User::factory()->create(), 'Cafetería Austral');

        $this->actingAs($admin)->get(route('admin.business.index'))
            ->assertSee('Barbería Patagonia')
            ->assertSee('Cafetería Austral');

        $this->actingAs($admin)->get(route('admin.business.index', ['q' => 'Barbería']))
            ->assertSee('Barbería Patagonia')
            ->assertDontSee('Cafetería Austral');
    }

    public function test_ficha_muestra_datos_y_url_estable(): void
    {
        $admin = $this->admin();
        $business = $this->negocioDe(User::factory()->create());

        $this->actingAs($admin)->get(route('admin.business.show', $business))
            ->assertOk()
            ->assertSee($business->name)
            ->assertSee('/'.$business->slug)
            ->assertSee('Perfil Digital');
    }

    public function test_admin_activa_y_desactiva_modulos_de_pago(): void
    {
        $admin = $this->admin();
        $business = $this->negocioDe(User::factory()->create());

        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Reservations->value]));
        $this->assertTrue($business->fresh()->isModuleActive(Module::Reservations));

        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Reservations->value]));
        $this->assertFalse($business->fresh()->isModuleActive(Module::Reservations));
    }

    public function test_el_perfil_digital_no_se_puede_desactivar_desde_administracion(): void
    {
        $admin = $this->admin();
        $business = $this->negocioDe(User::factory()->create());

        $this->actingAs($admin)->post(route('admin.business.module', [$business, Module::Profile->value]))
            ->assertSessionHas('error');

        $this->assertTrue($business->fresh()->isModuleActive(Module::Profile));
    }

    public function test_pausar_y_reactivar_una_cuenta(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create(['password' => bcrypt('secret123')]);
        $business = $this->negocioDe($owner);

        $this->actingAs($admin)->post(route('admin.business.pause', $business));

        $business->refresh();
        $this->assertTrue($business->is_paused);

        $this->get(route('p.show', $business->slug))->assertNotFound();

        $this->post('/logout');
        $this->post('/login', ['email' => $owner->email, 'password' => 'secret123'])
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)->post(route('admin.business.pause', $business));

        $business->refresh();
        $this->assertFalse($business->is_paused);

        $this->get(route('p.show', $business->slug))->assertOk();
        $this->post('/logout');
        $this->post('/login', ['email' => $owner->email, 'password' => 'secret123'])
            ->assertRedirect(route('panel.index'));
    }

    public function test_editar_datos_y_restablecer_contrasena_de_una_cuenta(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $business = $this->negocioDe($owner);

        $this->actingAs($admin)->post(route('admin.business.account', $business), [
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
        ])->assertSessionHas('status');

        $this->assertEquals('nuevo@example.com', $owner->fresh()->email);

        $this->actingAs($admin)->post(route('admin.business.password', $business), [
            'password' => 'claveNueva99',
            'password_confirmation' => 'claveNueva99',
        ])->assertSessionHas('status');

        $this->post('/logout');
        $this->post('/login', ['email' => 'nuevo@example.com', 'password' => 'claveNueva99'])
            ->assertRedirect(route('panel.index'));
    }

    public function test_el_admin_puede_editar_la_url_publica_del_negocio(): void
    {
        $admin = $this->admin();
        $business = $this->negocioDe(User::factory()->create(), 'Barbería Patagonia');
        $oldSlug = $business->slug;

        $this->actingAs($admin)->post(route('admin.business.slug', $business), ['slug' => 'barberia-patagonia-oficial'])
            ->assertSessionHas('status');

        $business->refresh();
        $this->assertEquals('barberia-patagonia-oficial', $business->slug);

        // La URL anterior sigue redirigiendo (301) a la nueva.
        $this->get(route('p.show', $oldSlug))
            ->assertRedirect(route('p.show', $business->slug));

        // La nueva URL responde el perfil.
        $this->get(route('p.show', $business->slug))->assertOk();
    }

    public function test_no_se_permite_una_url_reservada_o_repetida(): void
    {
        $admin = $this->admin();
        $negocioA = $this->negocioDe(User::factory()->create(), 'Negocio Uno');
        $negocioB = $this->negocioDe(User::factory()->create(), 'Negocio Dos');
        $slugOriginal = $negocioA->slug;

        $this->actingAs($admin)->post(route('admin.business.slug', $negocioA), ['slug' => 'admin'])
            ->assertSessionHas('error');

        $this->actingAs($admin)->post(route('admin.business.slug', $negocioA), ['slug' => $negocioB->slug])
            ->assertSessionHas('error');

        $this->assertEquals($slugOriginal, $negocioA->fresh()->slug);
    }

    public function test_el_admin_elimina_una_cuenta_y_sus_datos(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $business = $this->negocioDe($owner, 'Negocio a borrar');
        $business->clients()->create(['name' => 'Cliente X', 'phone' => '+56911111111']);
        $kit = \App\Models\PhysicalCode::create([
            'type' => 'qr_nfc', 'serial' => 'KIT-DEL01', 'status' => \App\Models\PhysicalCode::STATUS_ACTIVATED,
            'business_id' => $business->id, 'activated_at' => now(),
        ]);

        $this->actingAs($admin)->delete(route('admin.business.destroy', $business))
            ->assertRedirect(route('admin.business.index'));

        $this->assertNull(User::find($owner->id));
        $this->assertNull(Business::find($business->id));
        $this->assertEquals(0, \App\Models\Client::where('business_id', $business->id)->count());

        $kit->refresh();
        $this->assertNull($kit->business_id);
        $this->assertEquals(\App\Models\PhysicalCode::STATUS_SOLD, $kit->status);
    }

    public function test_no_se_elimina_la_cuenta_de_un_administrador(): void
    {
        $admin = $this->admin();
        $business = $this->negocioDe($admin, 'Negocio del Admin');

        $this->actingAs($admin)->delete(route('admin.business.destroy', $business))
            ->assertSessionHas('error');

        $this->assertNotNull(Business::find($business->id));
    }

    public function test_personificar_un_negocio_y_volver(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $business = $this->negocioDe($owner);

        $this->actingAs($admin)->post(route('admin.business.impersonate', $business))
            ->assertRedirect(route('panel.index'));

        $this->assertAuthenticatedAs($owner);
        $this->assertTrue(session()->has('admin_original_user_id'));

        $this->get(route('panel.index'))->assertSee('Estás actuando como');

        // Mientras se personifica, no se puede entrar al área de administración.
        $this->get(route('admin.index'))->assertForbidden();

        // Se actúa como el dueño: desactiva el módulo reservas desde su panel.
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);

        $this->actingAs($owner)->post(route('admin.impersonate.leave'))
            ->assertRedirect(route('admin.index'));

        $this->assertAuthenticatedAs($admin);
        $this->assertFalse(session()->has('admin_original_user_id'));
    }

    public function test_no_se_puede_personificar_la_propia_cuenta(): void
    {
        $admin = $this->admin();
        $suNegocio = $this->negocioDe($admin, 'Negocio del Admin');

        $this->actingAs($admin)->post(route('admin.business.impersonate', $suNegocio))
            ->assertForbidden();
    }
}
