<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\PhysicalCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhysicalKitTest extends TestCase
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

    public function test_el_escaneo_de_un_kit_activo_registra_estadistica(): void
    {
        [$owner, $business] = $this->negocio();
        $code = $this->kitDisponible('AL-0007');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $code->serial]);

        $this->get(route('k.show', $code->serial))->assertRedirect(route('p.show', $business->slug));
        $this->get(route('k.show', $code->serial))->assertRedirect(route('p.show', $business->slug));

        $this->assertEquals(2, app(\App\Services\AnalyticsService::class)->counts($business)['qr']);
    }

    public function test_el_qr_del_kit_se_genera_automaticamente_con_su_serial(): void
    {
        $admin = $this->admin();
        $code = $this->kitDisponible('AL-0030');

        $response = $this->actingAs($admin)->get(route('admin.kits.qr', $code));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());

        $download = $this->actingAs($admin)->get(route('admin.kits.qr', ['code' => $code, 'descargar' => 1]));
        $this->assertStringContainsString('attachment', $download->headers->get('Content-Disposition'));
    }

    public function test_admin_puede_activar_un_kit_por_el_negocio(): void
    {
        $admin = $this->admin();
        [, $business] = $this->negocio();
        $code = $this->kitDisponible('AL-0031');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($admin)->post(route('admin.kits.activate', $business), ['code_id' => $code->id])
            ->assertSessionHas('status');

        $code->refresh();
        $this->assertEquals(PhysicalCode::STATUS_ACTIVATED, $code->status);
        $this->assertEquals($business->id, $code->business_id);
    }

    public function test_el_serial_se_genera_automaticamente_si_se_deja_vacio(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.kits.store'), ['type' => 'qr_nfc', 'serial' => ''])
            ->assertSessionHas('status');

        $this->actingAs($admin)->post(route('admin.kits.store'), ['type' => 'qr_nfc']);

        $kits = PhysicalCode::where('status', PhysicalCode::STATUS_AVAILABLE)->get();
        $this->assertCount(2, $kits);
        foreach ($kits as $kit) {
            $this->assertStringStartsWith('KIT-', $kit->serial);
            $this->assertEquals(1, PhysicalCode::where('serial', $kit->serial)->count(), 'el serial es único');
        }
    }

    public function test_admin_gestiona_el_stock_de_kits_desde_la_seccion_kits(): void
    {
        $admin = $this->admin();
        $code = $this->kitDisponible('AL-0020');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($admin)->get(route('admin.kits.index'))
            ->assertOk()
            ->assertSee('Kits')
            ->assertSee('AL-0020')
            ->assertSee('Vendido');
    }

    private function kitDisponible(string $serial = 'AL-0001'): PhysicalCode
    {
        return PhysicalCode::create([
            'type' => PhysicalCode::TYPE_QR_NFC,
            'serial' => $serial,
            'status' => PhysicalCode::STATUS_AVAILABLE,
        ]);
    }

    public function test_admin_vende_un_kit_disponible(): void
    {
        $admin = $this->admin();
        $code = $this->kitDisponible();

        $this->actingAs($admin)->post(route('admin.kits.sell', $code))
            ->assertSessionHas('status');

        $code->refresh();
        $this->assertEquals(PhysicalCode::STATUS_SOLD, $code->status);
        $this->assertNull($code->business_id, 'la venta no asigna negocio');
    }

    public function test_no_se_vende_un_kit_ya_vendido(): void
    {
        $admin = $this->admin();
        $code = $this->kitDisponible();
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($admin)->post(route('admin.kits.sell', $code))
            ->assertSessionHas('error');
    }

    public function test_el_dueño_activa_su_kit_vendido(): void
    {
        [$owner, $business] = $this->negocio();
        $code = $this->kitDisponible();
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $code->serial])
            ->assertSessionHas('status');

        $code->refresh();
        $this->assertEquals(PhysicalCode::STATUS_ACTIVATED, $code->status);
        $this->assertEquals($business->id, $code->business_id);
        $this->assertNotNull($code->activated_at);
    }

    public function test_validaciones_de_activacion(): void
    {
        [$owner, $business] = $this->negocio();
        [$otroOwner, $otroBusiness] = $this->negocio();

        $code = $this->kitDisponible('AL-0002');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => 'NO-EXISTE'])
            ->assertSessionHas('error', 'El código ingresado no es válido.');

        $available = $this->kitDisponible('AL-0003');
        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $available->serial])
            ->assertSessionHas('error', 'Este kit aún no se ha vendido.');

        $code->update(['status' => PhysicalCode::STATUS_ACTIVATED, 'business_id' => $otroBusiness->id, 'activated_at' => now()]);

        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $code->serial])
            ->assertSessionHas('error', 'Este código ya está activado en otro negocio.');

        $this->assertEquals($otroBusiness->id, $code->fresh()->business_id);

        $this->actingAs($otroOwner)->post(route('panel.kit.activate'), ['serial' => $code->serial])
            ->assertSessionHas('status', 'Este kit ya está activado en tu negocio.');
    }

    public function test_kit_no_activado_muestra_pagina_y_serial_inexistente_404(): void
    {
        $code = $this->kitDisponible('AL-0004');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->get(route('k.show', $code->serial))
            ->assertOk()
            ->assertSee('Este código aún no está activado');

        $this->get(route('k.show', 'NO-EXISTE'))->assertNotFound();
    }

    public function test_kit_activado_redirige_al_perfil_del_negocio(): void
    {
        [$owner, $business] = $this->negocio();
        $code = $this->kitDisponible('AL-0005');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $code->serial]);

        $this->get(route('k.show', $code->serial))
            ->assertRedirect(route('p.show', $business->slug));
    }

    public function test_kit_funciona_con_perfil_gratuito_sin_modulos_de_pago(): void
    {
        [$owner, $business] = $this->negocio();
        $code = $this->kitDisponible('AL-0006');
        $code->update(['status' => PhysicalCode::STATUS_SOLD]);

        $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $code->serial]);

        $this->assertFalse($business->fresh()->isModuleActive(\App\Enums\Module::Catalog));

        $this->get(route('k.show', $code->serial))
            ->assertRedirect(route('p.show', $business->slug));
    }

    public function test_un_negocio_puede_activar_varios_kits(): void
    {
        [$owner, $business] = $this->negocio();
        $k1 = $this->kitDisponible('AL-0010');
        $k2 = $this->kitDisponible('AL-0011');
        $k1->update(['status' => PhysicalCode::STATUS_SOLD]);
        $k2->update(['status' => PhysicalCode::STATUS_SOLD]);

        foreach ([$k1, $k2] as $kit) {
            $this->actingAs($owner)->post(route('panel.kit.activate'), ['serial' => $kit->serial]);
        }

        $this->assertEquals(2, PhysicalCode::where('business_id', $business->id)->where('status', PhysicalCode::STATUS_ACTIVATED)->count());
    }
}
