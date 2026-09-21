<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\TicketeraEvent;
use App\Models\TicketeraOrder;
use App\Models\TicketeraStaff;
use App\Models\TicketeraTicket;
use App\Models\TicketeraTicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketeraTest extends TestCase
{
    use RefreshDatabase;

    private function negocio(string $name = 'Productora Sur'): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, $name);
        $business->moduleAccess()->where('module', Module::Ticketera->value)->update(['active' => true]);

        return [$user, $business];
    }

    private function evento(Business $business, array $overrides = []): TicketeraEvent
    {
        return TicketeraEvent::create(array_merge([
            'business_id' => $business->id,
            'name' => 'Fiesta Patagonia 2026',
            'slug' => TicketeraEvent::uniqueSlug('Fiesta Patagonia 2026'),
            'starts_at' => now()->addMonth(),
            'status' => 'published',
        ], $overrides));
    }

    private function tipo(TicketeraEvent $event, int $price = 10000, int $stock = 100): TicketeraTicketType
    {
        return $event->ticketTypes()->create([
            'business_id' => $event->business_id,
            'name' => 'General',
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    public function test_el_modulo_aparece_en_el_panel_y_requiere_activacion(): void
    {
        [$user] = $this->negocio();

        $this->actingAs($user)->get(route('panel.modules'))->assertOk()->assertSee('Ticketera');
        $this->actingAs($user)->get(route('panel.ticketera.index'))->assertOk()->assertSee('Ticketera');

        [$user2, $business2] = $this->negocio('Otra Productora');
        $business2->moduleAccess()->where('module', Module::Ticketera->value)->update(['active' => false]);

        $this->actingAs($user2)->get(route('panel.ticketera.index'))->assertNotFound();
    }

    public function test_crear_evento_y_tipo_de_entrada(): void
    {
        [$user, $business] = $this->negocio();

        $this->actingAs($user)->post(route('panel.ticketera.events.store'), [
            'name' => 'Concierto Austral',
            'starts_at' => now()->addMonth()->format('Y-m-d\TH:i'),
            'status' => 'published',
        ])->assertRedirect();

        $event = TicketeraEvent::first();
        $this->assertNotNull($event);

        $this->actingAs($user)->post(route('panel.ticketera.types.store', $event), [
            'name' => 'VIP',
            'price' => 20000,
            'stock' => 30,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertEquals(1, $event->ticketTypes()->count());
    }

    public function test_compra_genera_orden_y_entradas_individuales_con_qr_unico(): void
    {
        [, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event, 10000, 100);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 4,
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan@example.com',
            'phone' => '+56911111111',
        ])->assertRedirect();

        $order = TicketeraOrder::first();
        $this->assertEquals('ORD-'.now()->year.'-000001', $order->number);
        $this->assertEquals('paid', $order->status->value);
        $this->assertEquals(4, $order->tickets()->count());
        $this->assertEquals(40000, $order->total);

        $this->assertEquals(4, $order->tickets()->distinct()->count('token'));
        $this->assertEquals(4, $type->fresh()->sold);
        $this->assertEquals(96, $type->fresh()->remaining());
    }

    public function test_no_se_puede_sobrevender(): void
    {
        [, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event, 10000, 2);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 2,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ])->assertRedirect();

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'name' => 'Ana',
            'email' => 'ana@example.com',
        ])->assertSessionHas('error');

        $this->assertEquals(1, TicketeraOrder::count());
        $this->assertEquals(2, $type->fresh()->sold);
    }

    public function test_una_entrada_no_puede_usarse_dos_veces(): void
    {
        [$user, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $ticket = TicketeraTicket::first();

        $this->actingAs($user)->postJson(route('panel.ticketera.access.validate'), [
            'token' => $ticket->token,
            'event_id' => $event->id,
        ])->assertJsonPath('result', 'valid');

        $this->assertEquals('used', $ticket->fresh()->status->value);

        $this->actingAs($user)->postJson(route('panel.ticketera.access.validate'), [
            'token' => $ticket->token,
            'event_id' => $event->id,
        ])->assertJsonPath('result', 'used');
    }

    public function test_validacion_detecta_evento_incorrecto(): void
    {
        [$user, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event);
        $other = $this->evento($business, ['name' => 'Otro Evento', 'slug' => TicketeraEvent::uniqueSlug('Otro Evento')]);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $this->actingAs($user)->postJson(route('panel.ticketera.access.validate'), [
            'token' => TicketeraTicket::first()->token,
            'event_id' => $other->id,
        ])->assertJsonPath('result', 'wrong_event');
    }

    public function test_el_reembolso_invalida_las_entradas(): void
    {
        [$user, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event, 10000, 10);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 2,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $order = TicketeraOrder::first();

        $this->actingAs($user)->post(route('panel.ticketera.orders.refund', $order), ['reason' => 'Solicitud del cliente'])
            ->assertRedirect();

        $this->assertEquals('refunded', $order->fresh()->status->value);
        $this->assertEquals(0, $type->fresh()->sold);
        $this->assertEquals('refunded', TicketeraTicket::first()->fresh()->status->value);

        $this->actingAs($user)->postJson(route('panel.ticketera.access.validate'), [
            'token' => TicketeraTicket::first()->token,
            'event_id' => $event->id,
        ])->assertJsonPath('result', 'refunded');
    }

    public function test_cancelar_evento_invalida_las_entradas(): void
    {
        [$user, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $this->actingAs($user)->post(route('panel.ticketera.events.status', $event), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertEquals('cancelled', $event->fresh()->status->value);

        $this->actingAs($user)->postJson(route('panel.ticketera.access.validate'), [
            'token' => TicketeraTicket::first()->token,
            'event_id' => $event->id,
        ])->assertJsonPath('result', 'cancelled');
    }

    public function test_el_personal_de_acceso_valida_con_su_enlace(): void
    {
        [$user, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event);

        $this->actingAs($user)->post(route('panel.ticketera.staff.store'), ['name' => 'Puerta 1'])->assertRedirect();
        $staff = TicketeraStaff::first();
        $this->assertNotNull($staff);

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $this->get(route('ticketera.access', $staff->token))->assertOk()->assertSee('Escanear entrada');

        $this->postJson(route('ticketera.access.validate', $staff->token), [
            'token' => TicketeraTicket::first()->token,
            'event_id' => $event->id,
        ])->assertJsonPath('result', 'valid')->assertJsonPath('counters.entered', 1);

        $this->assertEquals($staff->id, TicketeraTicket::first()->access->staff_id);
    }

    public function test_pagina_publica_y_recuperacion(): void
    {
        [, $business] = $this->negocio();
        $event = $this->evento($business);
        $type = $this->tipo($event);

        $this->get(route('ticketera.event', $event->slug))->assertOk()->assertSee('Fiesta Patagonia 2026');

        $this->post(route('ticketera.checkout', $event->slug), [
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $order = TicketeraOrder::first();
        $ticket = TicketeraTicket::first();

        $this->get(route('ticketera.order', [$event->slug, $order->number, 'email' => 'juan@example.com']))->assertOk();
        $this->get(route('ticketera.ticket', $ticket->token))->assertOk()->assertSee($ticket->number);

        $this->post(route('ticketera.recover.submit'), ['email' => 'juan@example.com', 'number' => $order->number])
            ->assertRedirect();
    }

    public function test_un_evento_en_borrador_no_es_publico(): void
    {
        [, $business] = $this->negocio();
        $event = $this->evento($business, ['status' => 'draft']);

        $this->get(route('ticketera.event', $event->slug))->assertNotFound();
    }

    public function test_aislamiento_entre_negocios(): void
    {
        [$userA, $businessA] = $this->negocio('Productora A');
        [, $businessB] = $this->negocio('Productora B');
        $event = $this->evento($businessA);

        $this->actingAs($businessB->account)->get(route('panel.ticketera.events.show', $event))->assertNotFound();
    }
}
