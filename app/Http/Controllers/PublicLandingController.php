<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\KitRequest;
use App\Models\ModulePrice;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicLandingController extends Controller
{
    public function home(): View
    {
        $precios = ModulePrice::pluck('price_monthly', 'module');

        $modulosPago = collect(Module::cases())
            ->reject(fn ($m) => $m->isFree())
            ->map(fn (Module $m) => [
                'module' => $m,
                'label' => $m->label(),
                'price' => $precios[$m->value] ?? null,
            ])
            ->values();

        return view('public.landing', [
            'modulosPago' => $modulosPago,
            'preciosDefinidos' => ModulePrice::exists(),
            'kitPrice' => Setting::getInt(Setting::KIT_PRICE),
        ]);
    }

    public function storeKitRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ], [
            'name.required' => 'Ingresa tu nombre.',
            'email.email' => 'Ingresa un correo válido.',
            'quantity.integer' => 'La cantidad debe ser un número.',
        ]);

        KitRequest::create([
            'name' => $validated['name'],
            'business_name' => $validated['business_name'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'email' => $validated['email'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'status' => 'nueva',
        ]);

        return redirect()->to(route('landing.home').'#kit')
            ->with('kit_ok', '¡Gracias! Recibimos tu solicitud. Te contactaremos para coordinar tu kit.');
    }
}
