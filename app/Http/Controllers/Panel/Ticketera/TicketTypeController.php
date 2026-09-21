<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Models\TicketeraEvent;
use App\Models\TicketeraTicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketTypeController extends TicketeraController
{
    public function store(Request $request, TicketeraEvent $event): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['event_id'] = $event->id;
        $data['position'] = $event->ticketTypes()->count();

        $event->ticketTypes()->create($data);

        return redirect()->route('panel.ticketera.events.edit', $event)->with('status', 'Tipo de entrada agregado.');
    }

    public function update(Request $request, TicketeraEvent $event, TicketeraTicketType $type): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);
        abort_unless($type->event_id === $event->id, 404);

        $type->update($this->validated($request));

        return redirect()->route('panel.ticketera.events.edit', $event)->with('status', 'Tipo de entrada actualizado.');
    }

    public function toggle(TicketeraEvent $event, TicketeraTicketType $type): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);
        abort_unless($type->event_id === $event->id, 404);

        $type->update(['is_active' => ! $type->is_active]);

        return back()->with('status', $type->is_active ? 'Tipo de entrada activado.' : 'Tipo de entrada desactivado.');
    }

    public function destroy(TicketeraEvent $event, TicketeraTicketType $type): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);
        abort_unless($type->event_id === $event->id, 404);

        $type->delete();

        return back()->with('status', 'Tipo de entrada eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:400'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'sales_start_at' => ['nullable', 'date'],
            'sales_end_at' => ['nullable', 'date', 'after_or_equal:sales_start_at'],
            'purchase_limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Ponle un nombre al tipo de entrada.',
            'price.required' => 'Indica el precio.',
            'stock.required' => 'Indica cuántas entradas hay disponibles.',
            'sales_end_at.after_or_equal' => 'El fin de venta debe ser posterior al inicio.',
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}
