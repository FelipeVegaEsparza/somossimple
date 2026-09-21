<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Enums\TicketeraEventStatus;
use App\Models\TicketeraEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends TicketeraController
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $q = trim((string) $request->input('q'));

        $events = $business->ticketeraEvents()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->withCount(['tickets', 'orders'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('panel.ticketera.events.index', [
            'business' => $business,
            'events' => $events,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('panel.ticketera.events.form', [
            'business' => $this->business(),
            'event' => null,
            'statuses' => TicketeraEventStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();
        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['slug'] = TicketeraEvent::uniqueSlug($data['name']);
        $data['position'] = $business->ticketeraEvents()->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ticketera', 'public');
        }

        $event = TicketeraEvent::create($data);

        return redirect()->route('panel.ticketera.events.edit', $event)
            ->with('status', 'Evento creado. Agrega tus tipos de entrada y publícalo.');
    }

    public function show(TicketeraEvent $event): View
    {
        $business = $this->business();
        $this->guardEvent($business, $event);
        $event->load('ticketTypes');

        return view('panel.ticketera.events.show', [
            'business' => $business,
            'event' => $event,
            'counters' => $this->service()->counters($event),
        ]);
    }

    public function edit(TicketeraEvent $event): View
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        return view('panel.ticketera.events.form', [
            'business' => $business,
            'event' => $event,
            'statuses' => TicketeraEventStatus::cases(),
        ]);
    }

    public function update(Request $request, TicketeraEvent $event): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ticketera', 'public');
        }

        $event->update($data);

        return redirect()->route('panel.ticketera.events.edit', $event)->with('status', 'Evento actualizado.');
    }

    public function status(Request $request, TicketeraEvent $event): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TicketeraEventStatus::class)],
        ]);

        $status = TicketeraEventStatus::from($validated['status']);

        if ($status === TicketeraEventStatus::Cancelled) {
            $this->service()->cancelEvent($event);
        } else {
            $event->update(['status' => $status]);
        }

        return back()->with('status', 'El evento quedó en estado «'.$status->label().'».');
    }

    public function destroy(TicketeraEvent $event): RedirectResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $event->delete();

        return redirect()->route('panel.ticketera.events.index')->with('status', 'Evento eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:80'],
            'organizer' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'venue_name' => ['nullable', 'string', 'max:160'],
            'venue_address' => ['nullable', 'string', 'max:200'],
            'venue_city' => ['nullable', 'string', 'max:80'],
            'venue_info' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::enum(TicketeraEventStatus::class)],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'image' => ['nullable', 'image', 'max:4096'],
        ], [
            'name.required' => 'Ponle un nombre al evento.',
            'starts_at.required' => 'Indica la fecha y hora de inicio.',
            'ends_at.after_or_equal' => 'La hora de término debe ser posterior al inicio.',
        ]) + ['commission_rate' => $request->input('commission_rate', 0)];
    }
}
