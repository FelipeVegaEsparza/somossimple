<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Models\TicketeraOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends TicketeraController
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $q = trim((string) $request->input('q'));

        $orders = $business->ticketeraOrders()
            ->with('event')
            ->withCount('tickets')
            ->when($request->filled('event'), fn ($query) => $query->where('event_id', $request->integer('event')))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('number', 'like', "%{$q}%")
                        ->orWhere('buyer_name', 'like', "%{$q}%")
                        ->orWhere('buyer_lastname', 'like', "%{$q}%")
                        ->orWhere('buyer_email', 'like', "%{$q}%")
                        ->orWhere('buyer_phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('panel.ticketera.orders.index', [
            'business' => $business,
            'orders' => $orders,
            'events' => $business->ticketeraEvents()->get(),
            'q' => $q,
        ]);
    }

    public function show(TicketeraOrder $order): View
    {
        $business = $this->business();
        abort_unless($order->business_id === $business->id, 404);

        $order->load(['event', 'tickets.ticketType', 'refunds']);

        return view('panel.ticketera.orders.show', [
            'business' => $business,
            'order' => $order,
        ]);
    }

    public function refund(Request $request, TicketeraOrder $order): RedirectResponse
    {
        $business = $this->business();
        abort_unless($order->business_id === $business->id, 404);

        if ($order->status()->value === 'refunded') {
            return back()->with('error', 'Esta orden ya fue reembolsada.');
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $this->service()->refundOrder($order, $validated['reason'] ?? null);

        return back()->with('status', 'Orden reembolsada. Sus entradas quedaron invalidadas.');
    }
}
