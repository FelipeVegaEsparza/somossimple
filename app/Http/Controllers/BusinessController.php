<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->business) {
            return redirect()->route('panel.index')
                ->with('status', 'Tu cuenta ya tiene un negocio asociado.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Ingresa el nombre de tu negocio.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
        ]);

        $business = Business::createForAccount($user, $validated['name']);

        return redirect()->route('panel.index')
            ->with('status', 'Tu negocio «'.$business->name.'» fue creado. Empieza configurando tu perfil.');
    }
}
