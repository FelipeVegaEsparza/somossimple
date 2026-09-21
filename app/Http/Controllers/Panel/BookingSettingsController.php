<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BookingDayOff;
use App\Models\BookingService;
use App\Models\BookingWeekHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingSettingsController extends Controller
{
    public const DAY_NAMES = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.reservations.config', [
            'business' => $business,
            'dayNames' => self::DAY_NAMES,
            'weekHours' => $business->bookingWeekHours()->orderBy('day_of_week')->get()->keyBy('day_of_week'),
            'dayOffs' => $business->bookingDayOffs()->orderBy('date')->get(),
        ]);
    }

    public function storeService(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Ingresa el nombre del servicio.',
            'duration_minutes.required' => 'Ingresa la duración en minutos.',
        ]);

        $business->bookingServices()->create([
            'name' => $validated['name'],
            'duration_minutes' => $validated['duration_minutes'],
            'price' => $request->filled('price') ? (int) $validated['price'] : null,
            'position' => $business->bookingServices()->count(),
        ]);

        return back()->with('status', 'Servicio agregado.');
    }

    public function toggleService(BookingService $service): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->update(['active' => ! $service->active]);

        return back()->with('status', $service->active ? 'Servicio activado.' : 'Servicio desactivado.');
    }

    public function destroyService(BookingService $service): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->delete();

        return back()->with('status', 'Servicio eliminado.');
    }

    public function updateHours(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $days = $request->validate([
            'days' => ['required', 'array'],
            'days.*.open' => ['nullable', 'date_format:H:i'],
            'days.*.close' => ['nullable', 'date_format:H:i'],
        ])['days'];

        $business->bookingWeekHours()->delete();

        foreach ($days as $day => $range) {
            $open = $range['open'] ?? null;
            $close = $range['close'] ?? null;

            if ($open && $close) {
                $business->bookingWeekHours()->create([
                    'day_of_week' => (int) $day,
                    'open_time' => $open,
                    'close_time' => $close,
                ]);
            }
        }

        return back()->with('status', 'Horarios de atención guardados.');
    }

    public function storeDayOff(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $business->bookingDayOffs()->firstOrCreate(['date' => $validated['date']]);

        return back()->with('status', 'Día no disponible registrado.');
    }

    public function destroyDayOff(BookingDayOff $dayOff): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($dayOff->business_id !== $business->id) {
            abort(404);
        }

        $dayOff->delete();

        return back()->with('status', 'Día no disponible eliminado.');
    }
}
