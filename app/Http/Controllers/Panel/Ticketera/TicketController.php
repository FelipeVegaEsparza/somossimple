<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Models\TicketeraTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends TicketeraController
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $q = trim((string) $request->input('q'));

        $tickets = $business->ticketeraTickets()
            ->with(['event', 'order', 'ticketType'])
            ->when($request->filled('event'), fn ($query) => $query->where('event_id', $request->integer('event')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('number', 'like', "%{$q}%")
                        ->orWhere('token', 'like', "%{$q}%")
                        ->orWhere('holder_name', 'like', "%{$q}%")
                        ->orWhereHas('order', fn ($o) => $o->where('buyer_email', 'like', "%{$q}%"));
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('panel.ticketera.tickets.index', [
            'business' => $business,
            'tickets' => $tickets,
            'events' => $business->ticketeraEvents()->get(),
            'q' => $q,
        ]);
    }

    public function show(TicketeraTicket $ticket): View
    {
        $business = $this->business();
        abort_unless($ticket->business_id === $business->id, 404);

        $ticket->load(['event', 'order', 'ticketType', 'access']);

        return view('panel.ticketera.tickets.show', [
            'business' => $business,
            'ticket' => $ticket,
        ]);
    }
}
