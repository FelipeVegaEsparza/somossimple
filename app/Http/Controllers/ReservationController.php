<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Models\AnalyticsEvent;
use App\Models\BookingService;
use App\Models\Business;
use App\Models\Reservation;
use App\Services\AnalyticsService;
use App\Services\BookingAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function show(string $slug): View
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable() || ! $business->isModuleActive(Module::Reservations)) {
            abort(404);
        }

        return view('public.reservation', [
            'business' => $business,
            'services' => $business->bookingServices()->where('active', true)->get(),
        ]);
    }

    public function times(string $slug, Request $request): JsonResponse
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable() || ! $business->isModuleActive(Module::Reservations)) {
            abort(404);
        }

        $service = BookingService::ofBusiness($business)
            ->where('id', $request->integer('service_id'))
            ->where('active', true)
            ->first();

        if (! $service) {
            return response()->json(['times' => []]);
        }

        $date = CarbonImmutable::parse($request->input('date'))->startOfDay();

        $times = app(BookingAvailability::class)->availableTimes($business, $service, $date);

        return response()->json(['times' => $times]);
    }

    public function store(string $slug, Request $request): RedirectResponse
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable() || ! $business->isModuleActive(Module::Reservations)) {
            abort(404);
        }

        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'client_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'service_id.required' => 'Selecciona un servicio.',
            'date.required' => 'Selecciona una fecha.',
            'time.required' => 'Selecciona una hora disponible.',
            'client_name.required' => 'Ingresa tu nombre.',
            'phone.required' => 'Ingresa tu teléfono.',
            'email.email' => 'Ingresa un correo válido.',
        ]);

        $service = BookingService::ofBusiness($business)
            ->where('id', $validated['service_id'])
            ->where('active', true)
            ->firstOrFail();

        $startsAt = CarbonImmutable::parse($validated['date'].' '.$validated['time']);

        if (! app(BookingAvailability::class)->isFree($business, $service, $startsAt->toMutable())) {
            return back()->withErrors(['time' => 'Esa hora ya no está disponible. Elige otra.'])
                ->withInput();
        }

        $client = app(\App\Services\ClientService::class)->resolve(
            $business,
            $validated['client_name'],
            $validated['phone'],
            $validated['email'] ?? null,
        );

        $reservation = Reservation::create([
            'business_id' => $business->id,
            'service_id' => $service->id,
            'client_id' => $client->id,
            'service_name' => $service->name,
            'duration_minutes' => $service->duration_minutes,
            'starts_at' => $startsAt,
            'client_name' => $validated['client_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'status' => Reservation::STATUS_PENDING,
        ]);

        app(AnalyticsService::class)->record($business, AnalyticsEvent::TYPE_RESERVATION);

        return redirect()->route('p.reservation', $business->slug)
            ->with('reserva_ok', [
                'service' => $service->name,
                'date' => $startsAt->translatedFormat('l d \d\e F'),
                'time' => $startsAt->format('H:i'),
            ]);
    }
}
