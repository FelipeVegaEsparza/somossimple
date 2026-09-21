<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\PhysicalCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.kit.index', [
            'business' => $business,
            'kits' => $business ? PhysicalCode::where('business_id', $business->id)->where('status', PhysicalCode::STATUS_ACTIVATED)->get() : collect(),
        ]);
    }

    public function activate(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        if (! $business) {
            return back()->with('error', 'Primero crea tu negocio.');
        }

        $validated = $request->validate([
            'serial' => ['required', 'string', 'max:120'],
        ], ['serial.required' => 'Ingresa el serial de tu kit.']);

        $code = PhysicalCode::where('serial', trim($validated['serial']))->first();

        if (! $code) {
            return back()->with('error', 'El código ingresado no es válido.');
        }

        if ($code->status === PhysicalCode::STATUS_AVAILABLE) {
            return back()->with('error', 'Este kit aún no se ha vendido.');
        }

        if ($code->status === PhysicalCode::STATUS_ACTIVATED) {
            if ($code->business_id === $business->id) {
                return back()->with('status', 'Este kit ya está activado en tu negocio.');
            }

            return back()->with('error', 'Este código ya está activado en otro negocio.');
        }

        $code->update([
            'status' => PhysicalCode::STATUS_ACTIVATED,
            'business_id' => $business->id,
            'activated_at' => now(),
        ]);

        return back()->with('status', 'Kit activado. Tus clientes ya pueden escanearlo para llegar a tu perfil.');
    }
}
