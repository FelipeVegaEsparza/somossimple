<?php

namespace App\Http\Controllers\Panel\Loyalty;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardController extends LoyaltyController
{
    public function edit(): View
    {
        $business = $this->business();

        return view('panel.loyalty.card', [
            'business' => $business,
            'program' => $this->program($business),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $business = $this->business();
        $program = $this->program($business);

        $validated = $request->validate([
            'card_primary_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'card_secondary_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'card_text_primary' => ['nullable', 'string', 'max:60'],
            'card_text_secondary' => ['nullable', 'string', 'max:120'],
        ], [
            'card_primary_color.regex' => 'Usa un color hexadecimal válido, por ejemplo #437eff.',
            'card_secondary_color.regex' => 'Usa un color hexadecimal válido, por ejemplo #1a2233.',
        ]);

        $program->update($validated);

        return redirect()->route('panel.loyalty.card')->with('status', 'Tarjeta actualizada.');
    }
}
