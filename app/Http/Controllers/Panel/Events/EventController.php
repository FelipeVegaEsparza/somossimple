<?php

namespace App\Http\Controllers\Panel\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.events.index', [
            'business' => $business,
            'events' => Event::ofBusiness($business)->orderBy('position')->get(),
        ]);
    }

    public function create(): View
    {
        return view('panel.events.form', [
            'event' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['position'] = Event::ofBusiness($business)->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('eventos', 'public');
        }

        Event::create($data);

        return redirect()->route('panel.events.index')->with('status', 'Evento creado.');
    }

    public function edit(Event $event): View
    {
        $business = auth()->user()->business;

        if ($event->business_id !== $business->id) {
            abort(404);
        }

        return view('panel.events.form', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($event->business_id !== $business->id) {
            abort(404);
        }

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('eventos', 'public');
        }

        $event->update($data);

        return redirect()->route('panel.events.index')->with('status', 'Evento actualizado.');
    }

    public function toggle(Event $event): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($event->business_id !== $business->id) {
            abort(404);
        }

        $event->update(['active' => ! $event->active]);

        return back()->with('status', $event->active ? 'Evento activado.' : 'Evento desactivado.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($event->business_id !== $business->id) {
            abort(404);
        }

        $event->delete();

        return back()->with('status', 'Evento eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Ingresa el título del evento.',
            'starts_at.required' => 'Ingresa la fecha y hora de inicio.',
            'starts_at.date' => 'La fecha de inicio no es válida.',
            'ends_at.after_or_equal' => 'La fecha de término debe ser posterior al inicio.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
        ]) + ['active' => $request->boolean('active')];
    }
}
