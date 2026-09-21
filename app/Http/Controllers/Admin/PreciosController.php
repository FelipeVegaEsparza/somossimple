<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\ModulePrice;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreciosController extends Controller
{
    public function index(): View
    {
        $paidModules = collect(Module::cases())->reject(fn ($m) => $m->isFree())->values();
        $prices = ModulePrice::pluck('price_monthly', 'module');

        return view('admin.billing.prices', [
            'paidModules' => $paidModules,
            'prices' => $prices,
            'kitPrice' => Setting::getInt(Setting::KIT_PRICE),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['required', 'integer', 'min:100'],
            'kit_price' => ['nullable', 'integer', 'min:100'],
        ], [
            'prices.*.required' => 'Cada módulo debe tener un precio mensual.',
            'prices.*.integer' => 'El precio debe ser un número en CLP.',
            'prices.*.min' => 'El precio mínimo es $100.',
            'kit_price.min' => 'El precio mínimo del kit es $100.',
        ]);

        foreach ($validated['prices'] as $module => $price) {
            if (Module::tryFrom($module)?->isFree()) {
                continue;
            }

            ModulePrice::updateOrCreate(
                ['module' => $module],
                ['price_monthly' => (int) $price],
            );
        }

        Setting::set(Setting::KIT_PRICE, $request->filled('kit_price') ? (string) (int) $validated['kit_price'] : null);

        return back()->with('status', 'Precios actualizados.');
    }
}
