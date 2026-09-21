<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Models\Business;
use App\Models\LoyaltyReward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RewardController extends LoyaltyController
{
    public function index(): View
    {
        $business = $this->business();

        return view('panel.loyalty.rewards.index', [
            'business' => $business,
            'program' => $this->program($business),
            'rewards' => $business->loyaltyRewards()->get(),
        ]);
    }

    public function create(): View
    {
        $business = $this->business();

        return view('panel.loyalty.rewards.form', [
            'business' => $business,
            'program' => $this->program($business),
            'reward' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['position'] = $business->loyaltyRewards()->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('fidelizacion', 'public');
        }

        LoyaltyReward::create($data);

        return redirect()->route('panel.loyalty.rewards.index')->with('status', 'Recompensa creada.');
    }

    public function edit(LoyaltyReward $reward): View
    {
        $business = $this->guard($reward);

        return view('panel.loyalty.rewards.form', [
            'business' => $business,
            'program' => $this->program($business),
            'reward' => $reward,
        ]);
    }

    public function update(Request $request, LoyaltyReward $reward): RedirectResponse
    {
        $this->guard($reward);

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('fidelizacion', 'public');
        }

        $reward->update($data);

        return redirect()->route('panel.loyalty.rewards.index')->with('status', 'Recompensa actualizada.');
    }

    public function toggle(LoyaltyReward $reward): RedirectResponse
    {
        $this->guard($reward);

        $reward->update(['is_active' => ! $reward->is_active]);

        return back()->with('status', $reward->is_active ? 'Recompensa activada.' : 'Recompensa desactivada.');
    }

    public function destroy(LoyaltyReward $reward): RedirectResponse
    {
        $this->guard($reward);

        $reward->delete();

        return back()->with('status', 'Recompensa eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:600'],
            'requirement_type' => ['required', 'in:points,visits,stamps'],
            'requirement_units' => ['required', 'integer', 'min:1', 'max:1000000'],
            'valid_until' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Ponle un nombre a la recompensa.',
            'requirement_units.required' => 'Indica cuántas unidades se necesitan.',
            'requirement_units.min' => 'El requisito debe ser al menos 1.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function guard(LoyaltyReward $reward): Business
    {
        $business = $this->business();

        abort_unless($reward->business_id === $business->id, 404);

        return $business;
    }
}
