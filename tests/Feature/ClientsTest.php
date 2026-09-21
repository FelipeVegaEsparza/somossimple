<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\Client;
use App\Models\ClientConsent;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ClientService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientsTest extends TestCase
{
    use RefreshDatabase;

    private function negocioClientes(bool $conModulo = true): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');

        if ($conModulo) {
            $business->moduleAccess()->where('module', Module::Clients->value)->update(['active' => true]);
        }

        return [$user, $business];
    }

    private function activarReservas(Business $business): void
    {
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);
    }

    public function test_registro_manual_de_cliente_con_consentimiento_inicial_no_otorgado(): void
    {
        [$user, $business] = $this->negocioClientes();

        $this->actingAs($user)->post(route('panel.clients.store'), [
            'name' => 'Juan Pérez',
            'phone' => '+56 9 1234 5678',
            'email' => 'juan@example.com',
        ])->assertRedirect();

        $client = Client::ofBusiness($business)->first();
        $this->assertNotNull($client);
        $this->assertFalse($client->consents()->where('channel', 'email')->first()->granted);
    }

    public function test_dos_reservas_del_mismo_telefono_generan_un_solo_cliente(): void
    {
        [, $business] = $this->negocioClientes();
        $this->activarReservas($business);

        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $business->bookingWeekHours()->create(['day_of_week' => 0, 'open_time' => '09:00', 'close_time' => '13:00']);
        $monday = CarbonImmutable::now()->next(Carbon::MONDAY)->startOfDay();

        foreach (['09:00', '09:30'] as $time) {
            $this->post(route('p.reservation.store', $business->slug), [
                'service_id' => $service->id,
                'date' => $monday->toDateString(),
                'time' => $time,
                'client_name' => 'Juan Pérez',
                'phone' => $time === '09:00' ? '+56912345678' : '9 1234 5678',
            ]);
        }

        $this->assertEquals(1, Client::ofBusiness($business)->count());

        $client = Client::ofBusiness($business)->first();
        $this->assertEquals(2, $client->reservations()->count());
        $this->assertEquals('56912345678', $client->phone_normalized);
        $this->assertFalse($client->hasEmailConsent(), 'una reserva no otorga consentimiento comercial');
    }

    public function test_registro_voluntario_con_aceptacion_explicita(): void
    {
        [, $business] = $this->negocioClientes();

        $this->post(route('p.voluntary', $business->slug), [
            'name' => 'Ana Soto',
            'phone' => '+56999999999',
            'email' => 'ana@example.com',
            'consent' => '1',
        ])->assertRedirect(route('p.clients', $business->slug));

        $client = Client::ofBusiness($business)->where('phone_normalized', '56999999999')->first();
        $this->assertNotNull($client);
        $consent = $client->consents()->where('channel', 'email')->first();
        $this->assertTrue($consent->granted);
        $this->assertEquals(ClientConsent::SOURCE_VOLUNTARY, $consent->source);
    }

    public function test_registro_voluntario_sin_aceptacion_no_otorga_consentimiento(): void
    {
        [, $business] = $this->negocioClientes();

        $response = $this->post(route('p.voluntary', $business->slug), [
            'name' => 'Ana Soto',
            'phone' => '+56988888888',
            'email' => 'ana2@example.com',
        ]);
        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        }

        $client = Client::ofBusiness($business)->where('phone_normalized', '56988888888')->first();
        $this->assertNotNull($client, 'puede existir como cliente sin consentimiento');
        $this->assertFalse($client->consents()->where('channel', 'email')->first()->granted);
    }

    public function test_etiquetas_y_notas_por_cliente(): void
    {
        [$user, $business] = $this->negocioClientes();
        $client = Client::create(['business_id' => $business->id, 'name' => 'Carlos', 'phone' => '+56911111111', 'phone_normalized' => '56911111111']);

        $this->actingAs($user)->post(route('panel.clients.tag.attach', $client), ['name' => 'VIP']);
        $this->assertTrue($client->tags()->where('name', 'VIP')->exists());

        $tag = $business->clientTags()->where('name', 'VIP')->first();
        $this->actingAs($user)->delete(route('panel.clients.tag.detach', [$client, $tag]));
        $this->assertFalse($client->tags()->where('name', 'VIP')->exists());

        $this->actingAs($user)->post(route('panel.clients.note.store', $client), ['note' => 'Prefiere los martes.']);
        $this->assertEquals('Prefiere los martes.', $client->notes()->first()->note);
    }

    public function test_el_historial_muestra_reservas_relacionadas(): void
    {
        [$user, $business] = $this->negocioClientes();
        $client = Client::create(['business_id' => $business->id, 'name' => 'Diego', 'phone' => '+56922222222', 'phone_normalized' => '56922222222']);

        Reservation::create([
            'business_id' => $business->id, 'client_id' => $client->id,
            'service_name' => 'Corte clásico', 'duration_minutes' => 30,
            'starts_at' => now()->addDay(), 'client_name' => 'Diego', 'phone' => '+56922222222',
            'status' => Reservation::STATUS_CONFIRMED,
        ]);

        $this->actingAs($user)->get(route('panel.clients.show', $client))
            ->assertOk()
            ->assertSee('Corte clásico');
    }

    public function test_el_consentimiento_es_independiente_del_cliente(): void
    {
        [$user, $business] = $this->negocioClientes();
        $client = app(ClientService::class)->resolve($business, 'María', '+56933333333');

        $this->assertNotNull($client);
        $this->assertFalse($client->hasEmailConsent());

        $this->actingAs($user)->post(route('panel.clients.consent', $client), ['granted' => 1]);
        $this->assertTrue($client->fresh()->hasEmailConsent());

        $this->actingAs($user)->post(route('panel.clients.consent', $client), ['granted' => 0]);
        $this->assertFalse($client->fresh()->hasEmailConsent());
    }

    public function test_un_negocio_no_ve_los_clientes_de_otro(): void
    {
        [$user, $business] = $this->negocioClientes();
        $otroUser = User::factory()->create();
        $otroBusiness = Business::createForAccount($otroUser, 'Otra Barbería');
        $otroBusiness->moduleAccess()->where('module', Module::Clients->value)->update(['active' => true]);
        $clientAjeno = Client::create(['business_id' => $otroBusiness->id, 'name' => 'Ajeno', 'phone' => '+56944444444', 'phone_normalized' => '56944444444']);

        $this->actingAs($user)->get(route('panel.clients.show', $clientAjeno))->assertNotFound();
        $this->actingAs($user)->get(route('panel.clients.index'))->assertDontSee('Ajeno');
    }

    public function test_clientes_no_disponible_si_el_modulo_esta_inactivo(): void
    {
        [$user] = $this->negocioClientes(conModulo: false);

        $this->actingAs($user)->get(route('panel.clients.index'))->assertForbidden();
    }
}
