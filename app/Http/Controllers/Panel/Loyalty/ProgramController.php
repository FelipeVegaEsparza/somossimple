<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Enums\LoyaltyProgramType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramController extends LoyaltyController
{
    public function edit(): View
    {
        $business = $this->business();

        return view('panel.loyalty.program', [
            'business' => $business,
            'program' => $this->program($business),
            'types' => LoyaltyProgramType::cases(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $business = $this->business();
        $program = $this->program($business);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:600'],
            'type' => ['required', Rule::enum(LoyaltyProgramType::class)],
            'unit_name' => ['required', 'string', 'max:40'],
            'earn_amount' => ['nullable', 'integer', 'min:1', 'max:100000000'],
            'earn_units' => ['required', 'integer', 'min:1', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Ponle un nombre a tu programa.',
            'type.required' => 'Elige el tipo de programa.',
            'unit_name.required' => 'Define el nombre de la unidad.',
            'earn_units.required' => 'Indica cuántas unidades se entregan.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['earn_amount'] = $validated['type'] === LoyaltyProgramType::Points->value
            || $validated['type'] === LoyaltyProgramType::Rewards->value
                ? ($validated['earn_amount'] ?? 1000)
                : null;

        $program->update($validated);

        return redirect()->route('panel.loyalty.program')->with('status', 'Programa actualizado.');
    }
}
