<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\Client;
use App\Models\ClientConsent;
use App\Models\Communication;
use App\Models\CommunicationSend;
use App\Models\User;
use App\Services\ClientService;
use App\Services\CommunicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CommunicationsTest extends TestCase
{
    use RefreshDatabase;

    private function setupConDatos(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');
        $business->moduleAccess()->where('module', Module::Communications->value)->update(['active' => true]);

        $service = app(ClientService::class);

        $a = $service->resolve($business, 'Ana', '+56910000001', 'ana@example.com');
        $service->ensureConsent($a, ClientConsent::CHANNEL_EMAIL, true, ClientConsent::SOURCE_VOLUNTARY);

        $b = $service->resolve($business, 'Benja', '+56910000002', 'benja@example.com'); // sin consentimiento

        $c = $service->resolve($business, 'Cata', '+56910000003'); // sin email

        $vip = $business->clientTags()->create(['name' => 'VIP']);
        $d = $service->resolve($business, 'Diego', '+56910000004', 'diego@example.com');
        $service->ensureConsent($d, ClientConsent::CHANNEL_EMAIL, true, ClientConsent::SOURCE_MANUAL);
        $d->tags()->syncWithoutDetaching([$vip->id]);

        $e = $service->resolve($business, 'Ema', '+56910000005', 'ema@example.com'); // VIP sin consentimiento
        $e->tags()->syncWithoutDetaching([$vip->id]);

        return [$user, $business];
    }

    public function test_crear_una_comunicacion_comercial_como_borrador(): void
    {
        [$user, $business] = $this->setupConDatos();

        $this->actingAs($user)->post(route('panel.communications.store'), [
            'type' => 'promo',
            'subject' => '20% en corte + barba',
            'body' => 'Este viernes solo.',
            'audience' => 'all',
            'is_commercial' => '1',
        ])->assertRedirect(route('panel.communications.index'));

        $communication = $business->communications()->first();
        $this->assertNotNull($communication);
        $this->assertEquals(Communication::STATUS_DRAFT, $communication->status);
    }

    public function test_comunicacion_comercial_excluye_sin_consentimiento_y_sin_email(): void
    {
        [, $business] = $this->setupConDatos();
        $service = app(CommunicationService::class);

        $communication = $business->communications()->create([
            'type' => 'promo', 'is_commercial' => true, 'subject' => 'Sujeto', 'body' => 'Cuerpo',
            'audience' => Communication::AUDIENCE_ALL, 'status' => Communication::STATUS_DRAFT,
        ]);

        $recipients = $service->resolveRecipients($communication);

        $emails = $recipients->pluck('email')->all();
        $this->assertContains('ana@example.com', $emails);
        $this->assertContains('diego@example.com', $emails);
        $this->assertNotContains('benja@example.com', $emails, 'sin consentimiento no recibe comerciales');
        $this->assertNotContains('ema@example.com', $emails);
        $this->assertCount(2, $emails);
    }

    public function test_envio_a_una_etiqueta_respeta_consentimiento(): void
    {
        [, $business] = $this->setupConDatos();
        $service = app(CommunicationService::class);

        $communication = $business->communications()->create([
            'type' => 'promo', 'is_commercial' => true, 'subject' => 'VIP', 'body' => 'Solo VIP',
            'audience' => Communication::AUDIENCE_TAG, 'tag_name' => 'VIP', 'status' => Communication::STATUS_DRAFT,
        ]);

        $emails = $service->resolveRecipients($communication)->pluck('email')->all();

        $this->assertEquals(['diego@example.com'], $emails);
    }

    public function test_comunicacion_operacional_no_exige_consentimiento_comercial(): void
    {
        [, $business] = $this->setupConDatos();
        $service = app(CommunicationService::class);

        $communication = $business->communications()->create([
            'type' => 'cambio_horario', 'is_commercial' => false, 'subject' => 'Horario', 'body' => 'Cerramos martes',
            'audience' => Communication::AUDIENCE_ALL, 'status' => Communication::STATUS_DRAFT,
        ]);

        $emails = $service->resolveRecipients($communication)->pluck('email')->all();

        $this->assertCount(4, $emails, 'todos con email reciben el aviso operacional');
        $this->assertContains('benja@example.com', $emails);
        $this->assertContains('ema@example.com', $emails);
        $this->assertNotContains('cata@example.com', $emails);
    }

    public function test_enviar_registra_el_historial_y_marca_como_enviada(): void
    {
        Mail::fake();

        [$user, $business] = $this->setupConDatos();

        $communication = $business->communications()->create([
            'type' => 'promo', 'is_commercial' => true, 'subject' => 'Promo', 'body' => 'Oferta',
            'audience' => Communication::AUDIENCE_ALL, 'status' => Communication::STATUS_DRAFT,
        ]);

        $this->actingAs($user)->post(route('panel.communications.send', $communication))
            ->assertSessionHas('status');

        $communication->refresh();
        $this->assertEquals(Communication::STATUS_SENT, $communication->status);
        $this->assertNotNull($communication->sent_at);
        $this->assertEquals(2, $communication->sends()->count());

        Mail::assertSent(\App\Mail\BusinessMessage::class, 2);

        $this->actingAs($user)->get(route('panel.communications.index'))
            ->assertOk()
            ->assertSee('Promo');
    }

    public function test_no_se_puede_enviar_dos_veces_la_misma_comunicacion(): void
    {
        Mail::fake();

        [$user, $business] = $this->setupConDatos();

        $communication = $business->communications()->create([
            'type' => 'general', 'is_commercial' => true, 'subject' => 'X', 'body' => 'Y',
            'audience' => Communication::AUDIENCE_ALL, 'status' => Communication::STATUS_SENT, 'sent_at' => now(),
        ]);

        $this->actingAs($user)->post(route('panel.communications.send', $communication));

        Mail::assertNothingSent();
        $this->assertEquals(0, CommunicationSend::count());
    }

    public function test_comunicaciones_no_disponible_si_modulo_inactivo(): void
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'X');
        $business->moduleAccess()->where('module', Module::Communications->value)->update(['active' => false]);

        $this->actingAs($user)->get(route('panel.communications.index'))->assertForbidden();
    }
}
