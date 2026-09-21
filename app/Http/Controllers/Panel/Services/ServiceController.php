<?php

namespace App\Http\Controllers\Panel\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.services.index', [
            'business' => $business,
            'services' => Service::ofBusiness($business)->orderBy('position')->get(),
        ]);
    }

    public function create(): View
    {
        return view('panel.services.form', [
            'service' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['position'] = Service::ofBusiness($business)->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('servicios', 'public');
        }

        Service::create($data);

        return redirect()->route('panel.services.index')->with('status', 'Servicio creado.');
    }

    public function edit(Service $service): View
    {
        $business = auth()->user()->business;

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        return view('panel.services.form', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('servicios', 'public');
        }

        $service->update($data);

        return redirect()->route('panel.services.index')->with('status', 'Servicio actualizado.');
    }

    public function toggle(Service $service): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->update(['active' => ! $service->active]);

        return back()->with('status', $service->active ? 'Servicio activado.' : 'Servicio desactivado.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->delete();

        return back()->with('status', 'Servicio eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Ingresa el nombre del servicio.',
            'price.numeric' => 'El precio debe ser un número.',
            'duration_minutes.integer' => 'La duración debe ser en minutos.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
        ]) + ['active' => $request->boolean('active')];
    }
}
