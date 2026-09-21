<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\BookingService;
use App\Models\Business;
use App\Models\Reservation;
use App\Models\User;
use App\Services\BookingAvailability;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationsTest extends TestCase
{
    use RefreshDatabase;

    private function negocioReservable(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);

        return [$user, $business];
    }

    private function monday(): CarbonImmutable
    {
        return CarbonImmutable::now()->next(Carbon::MONDAY)->startOfDay();
    }

    private function abrirSemana(Business $business, array $dias = [0]): void
    {
        foreach ($dias as $dia) {
            $business->bookingWeekHours()->create([
                'day_of_week' => $dia,
                'open_time' => '09:00',
                'close_time' => '13:00',
            ]);
        }
    }

    public function test_los_servicios_reservables_son_independientes_del_catalogo(): void
    {
        [$user, $business] = $this->negocioReservable();
        $this->assertFalse($business->isModuleActive(Module::Catalog));

        $this->actingAs($user)->post(route('panel.reservations.service.store'), [
            'name' => 'Corte clásico',
            'duration_minutes' => 30,
            'price' => 12000,
        ])->assertSessionHas('status');

        $this->assertEquals(1, $business->bookingServices()->count());
        $this->assertTrue($business->bookingServices()->first()->active);
    }

    public function test_la_disponibilidad_respeta_horarios_y_dias_no_disponibles(): void
    {
        [, $business] = $this->negocioReservable();
        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $this->abrirSemana($business, [0]);

        $monday = $this->monday();
        $times = app(BookingAvailability::class)->availableTimes($business, $service, $monday);

        $this->assertCount(15, $times);
        $this->assertContains('09:00', $times);
        $this->assertContains('12:30', $times);
        $this->assertNotContains('13:00', $times);

        $business->bookingDayOffs()->create(['date' => $monday]);

        $this->assertSame([], app(BookingAvailability::class)->availableTimes($business, $service, $monday));
    }

    public function test_las_reservas_pendientes_y_confirmadas_bloquean_y_canceladas_liberan(): void
    {
        [, $business] = $this->negocioReservable();
        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $this->abrirSemana($business, [0]);

        $availability = app(BookingAvailability::class);
        $monday = $this->monday();

        $reservation = Reservation::create([
            'business_id' => $business->id,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'duration_minutes' => 30,
            'starts_at' => $monday->setTime(9, 0),
            'client_name' => 'Juan Pérez',
            'phone' => '+56911111111',
            'status' => Reservation::STATUS_PENDING,
        ]);

        $timesBloqueado = $availability->availableTimes($business, $service, $monday);
        $this->assertNotContains('09:00', $timesBloqueado);
        $this->assertNotContains('09:15', $timesBloqueado);
        $this->assertContains('09:30', $timesBloqueado);

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);
        $this->assertContains('09:00', $availability->availableTimes($business, $service, $monday));

        $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);
        $this->assertNotContains('09:00', $availability->availableTimes($business, $service, $monday));
    }

    public function test_un_visitante_solicita_una_reserva_con_email_opcional(): void
    {
        [, $business] = $this->negocioReservable();
        $service = $business->bookingServices()->create(['name' => 'Corte + barba', 'duration_minutes' => 45]);
        $this->abrirSemana($business, [0]);
        $monday = $this->monday();

        $this->get(route('p.reservation', $business->slug))->assertOk()->assertSee('Corte + barba');

        $this->post(route('p.reservation.store', $business->slug), [
            'service_id' => $service->id,
            'date' => $monday->toDateString(),
            'time' => '10:00',
            'client_name' => 'María González',
            'phone' => '+56922222222',
            'email' => 'maria@example.com',
        ])->assertSessionHas('reserva_ok');

        $reservation = Reservation::where('business_id', $business->id)->first();
        $this->assertNotNull($reservation);
        $this->assertEquals(Reservation::STATUS_PENDING, $reservation->status);
        $this->assertEquals('maria@example.com', $reservation->email);

        $this->post(route('p.reservation.store', $business->slug), [
            'service_id' => $service->id,
            'date' => $monday->toDateString(),
            'time' => '11:00',
            'client_name' => 'Sin correo',
            'phone' => '+56933333333',
        ]);

        $this->assertNull(Reservation::where('business_id', $business->id)->orderByDesc('id')->first()->email);
    }

    public function test_una_hora_tomada_ya_no_esta_disponible_al_solicitar(): void
    {
        [, $business] = $this->negocioReservable();
        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $this->abrirSemana($business, [0]);
        $monday = $this->monday();

        Reservation::create([
            'business_id' => $business->id,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'duration_minutes' => 30,
            'starts_at' => $monday->setTime(11, 0),
            'client_name' => 'Otro',
            'phone' => '+56944444444',
            'status' => Reservation::STATUS_PENDING,
        ]);

        $this->post(route('p.reservation.store', $business->slug), [
            'service_id' => $service->id,
            'date' => $monday->toDateString(),
            'time' => '11:00',
            'client_name' => 'María',
            'phone' => '+56955555555',
        ])->assertSessionHasErrors('time');

        $this->assertEquals(1, Reservation::where('business_id', $business->id)->count());
    }

    public function test_el_propietario_gestiona_estados_desde_el_panel(): void
    {
        [$user, $business] = $this->negocioReservable();
        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $this->abrirSemana($business, [0]);
        $monday = $this->monday();

        $reservation = Reservation::create([
            'business_id' => $business->id,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'duration_minutes' => 30,
            'starts_at' => $monday->setTime(10, 0),
            'client_name' => 'Juan',
            'phone' => '+56966666666',
            'status' => Reservation::STATUS_PENDING,
        ]);

        $this->actingAs($user)
            ->post(route('panel.reservations.update-status', $reservation), ['status' => 'confirmed'])
            ->assertSessionHas('status');

        $this->assertEquals(Reservation::STATUS_CONFIRMED, $reservation->fresh()->status);
    }

    public function test_la_agenda_consulta_por_dia_y_por_semana(): void
    {
        [$user, $business] = $this->negocioReservable();
        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $this->abrirSemana($business, [0, 1]);
        $monday = $this->monday();
        $tuesday = $monday->addDay();

        Reservation::create([
            'business_id' => $business->id, 'service_id' => $service->id, 'service_name' => 'Corte',
            'duration_minutes' => 30, 'starts_at' => $monday->setTime(9, 0),
            'client_name' => 'Cliente Lunes', 'phone' => '+56977777777', 'status' => 'pending',
        ]);
        Reservation::create([
            'business_id' => $business->id, 'service_id' => $service->id, 'service_name' => 'Corte',
            'duration_minutes' => 30, 'starts_at' => $tuesday->setTime(9, 30),
            'client_name' => 'Cliente Martes', 'phone' => '+56988888888', 'status' => 'confirmed',
        ]);

        $this->actingAs($user)->get(route('panel.reservations.index', ['date' => $monday->toDateString()]))
            ->assertSee('Cliente Lunes')
            ->assertDontSee('Cliente Martes');

        $this->actingAs($user)->get(route('panel.reservations.index', [
            'date' => $monday->toDateString(),
            'view' => 'week',
        ]))->assertSee('Cliente Lunes')->assertSee('Cliente Martes');
    }
}
