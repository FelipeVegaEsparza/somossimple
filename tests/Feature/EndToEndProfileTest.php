<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndToEndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_perfil_dinamico_de_extremo_a_extremo(): void
    {
        [$user, $business] = $this->negocio();
        $business->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => true]);
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);

        $business->bookingServices()->create(['name' => 'Corte clásico', 'duration_minutes' => 30, 'price' => 12000]);
        $business->bookingWeekHours()->create(['day_of_week' => 0, 'open_time' => '09:00', 'close_time' => '17:00']);
        $business->catalogItems()->create(['name' => 'Corte + barba', 'price_mode' => 'exact', 'price' => 18000, 'active' => true]);

        // Con ambos módulos activos el perfil enlaza el catálogo y reservas.
        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee('Explora')
            ->assertSee(route('p.reservation', $business->slug), false)
            ->assertDontSee('Agendar hora');

        $this->get(route('p.catalog', $business->slug))
            ->assertOk()
            ->assertSee('Corte + barba');

        // Se desactiva reservas: desaparece el acceso, el catálogo sigue.
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => false]);

        $this->get(route('p.show', $business->slug))
            ->assertDontSee('Agendar hora')
            ->assertDontSee(route('p.reservation', $business->slug), false);
        $this->get(route('p.catalog', $business->slug))->assertSee('Corte + barba');

        // Se desactiva catálogo: su página pública deja de existir.
        $business->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => false]);

        $this->get(route('p.show', $business->slug))
            ->assertDontSee('Agendar hora');
        $this->get(route('p.catalog', $business->slug))->assertNotFound();
    }

    public function test_ciclo_sin_perdida_de_datos_al_desactivar_y_reactivar_reservas(): void
    {
        [$user, $business] = $this->negocio();
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);
        $business->moduleAccess()->where('module', Module::Clients->value)->update(['active' => true]);

        $service = $business->bookingServices()->create(['name' => 'Corte', 'duration_minutes' => 30]);
        $business->bookingWeekHours()->create(['day_of_week' => 0, 'open_time' => '09:00', 'close_time' => '13:00']);
        $monday = Carbon::now()->next(Carbon::MONDAY)->startOfDay();

        $this->post(route('p.reservation.store', $business->slug), [
            'service_id' => $service->id,
            'date' => $monday->toDateString(),
            'time' => '09:30',
            'client_name' => 'Juan Pérez',
            'phone' => '+56912345678',
        ])->assertSessionHas('reserva_ok');

        $this->assertEquals(1, Reservation::count());
        $this->assertEquals(1, Client::count());

        // Desactivar reservas: funcionalidad pública fuera, datos intactos.
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => false]);

        $this->assertFalse($business->isModuleActive(Module::Reservations));
        $this->get(route('p.show', $business->slug))->assertDontSee(route('p.reservation', $business->slug), false);
        $this->assertEquals(1, $business->bookingServices()->count());
        $this->assertEquals(1, $business->reservations()->count());

        // Reactivar: la información continúa disponible.
        $business->moduleAccess()->where('module', Module::Reservations->value)->update(['active' => true]);
        $business->refresh();

        $this->assertTrue($business->isModuleActive(Module::Reservations));
        $this->get(route('p.show', $business->slug))->assertSee(route('p.reservation', $business->slug), false);
        $this->assertEquals(1, $business->reservations()->count());
    }

    private function negocio(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Barbería Patagonia');

        return [$user, $business];
    }
}
