<?php

namespace App\Http\Controllers\Panel\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.promotions.index', [
            'business' => $business,
            'promotions' => Promotion::ofBusiness($business)->orderBy('position')->get(),
        ]);
    }

    public function create(): View
    {
        return view('panel.promotions.form', [
            'promotion' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['position'] = Promotion::ofBusiness($business)->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('promociones', 'public');
        }

        Promotion::create($data);

        return redirect()->route('panel.promotions.index')->with('status', 'Promoción creada.');
    }

    public function edit(Promotion $promotion): View
    {
        $business = auth()->user()->business;

        if ($promotion->business_id !== $business->id) {
            abort(404);
        }

        return view('panel.promotions.form', [
            'promotion' => $promotion,
        ]);
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($promotion->business_id !== $business->id) {
            abort(404);
        }

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('promociones', 'public');
        }

        $promotion->update($data);

        return redirect()->route('panel.promotions.index')->with('status', 'Promoción actualizada.');
    }

    public function toggle(Promotion $promotion): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($promotion->business_id !== $business->id) {
            abort(404);
        }

        $promotion->update(['active' => ! $promotion->active]);

        return back()->with('status', $promotion->active ? 'Promoción activada.' : 'Promoción desactivada.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($promotion->business_id !== $business->id) {
            abort(404);
        }

        $promotion->delete();

        return back()->with('status', 'Promoción eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Ingresa el título de la promoción.',
            'starts_on.date' => 'La fecha de inicio no es válida.',
            'ends_on.after_or_equal' => 'La fecha de término debe ser posterior al inicio.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
        ]) + ['active' => $request->boolean('active')];
    }
}
