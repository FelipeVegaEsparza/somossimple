<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationsController extends Controller
{
    public function index(Request $request): View
    {
        $business = auth()->user()->business;
        $date = Carbon::parse($request->input('date', today()->toDateString()))->startOfDay();
        $view = $request->input('view', 'day') === 'week' ? 'week' : 'day';

        if ($view === 'week') {
            $start = $date->copy()->startOfWeek();
            $reservations = Reservation::ofBusiness($business)
                ->whereBetween('starts_at', [$start, $start->copy()->addWeek()])
                ->orderBy('starts_at')
                ->get()
                ->groupBy(fn (Reservation $r) => $r->starts_at->toDateString());
        } else {
            $reservations = Reservation::ofBusiness($business)
                ->whereDate('starts_at', $date)
                ->orderBy('starts_at')
                ->get();
        }

        return view('panel.reservations.index', [
            'business' => $business,
            'date' => $date,
            'view' => $view,
            'reservations' => $reservations,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function updateStatus(Reservation $reservation, Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($reservation->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $reservation->update(['status' => $validated['status']]);

        return back()->with('status', 'Reserva actualizada.');
    }

    public function statusLabels(): array
    {
        return [
            Reservation::STATUS_PENDING => 'Pendiente',
            Reservation::STATUS_CONFIRMED => 'Confirmada',
            Reservation::STATUS_CANCELLED => 'Cancelada',
            Reservation::STATUS_COMPLETED => 'Completada',
        ];
    }
}
