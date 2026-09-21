<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Enums\TicketeraEventStatus;
use App\Enums\TicketeraTicketStatus;
use Illuminate\View\View;

class DashboardController extends TicketeraController
{
    public function index(): View
    {
        $business = $this->business();

        return view('panel.ticketera.dashboard', [
            'business' => $business,
            'stats' => [
                'active_events' => $business->ticketeraEvents()->where('status', TicketeraEventStatus::Published->value)->count(),
                'tickets_sold' => $business->ticketeraTickets()->whereIn('status', [TicketeraTicketStatus::Issued->value, TicketeraTicketStatus::Used->value])->count(),
                'revenue' => (int) $business->ticketeraOrders()->where('status', 'paid')->sum('total'),
                'available' => (int) $business->ticketeraEvents()->get()->sum(fn ($event) => $event->availableTickets()),
                'used' => $business->ticketeraTickets()->where('status', TicketeraTicketStatus::Used->value)->count(),
            ],
            'upcoming' => $business->ticketeraEvents()
                ->where('status', TicketeraEventStatus::Published->value)
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
            'recentOrders' => $business->ticketeraOrders()->with('event')->latest()->limit(8)->get(),
        ]);
    }
}
