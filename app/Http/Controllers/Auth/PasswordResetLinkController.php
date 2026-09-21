<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Ingresa un correo válido.',
        ]);

        Password::sendResetLink($request->only('email'));

        // No se revela si el correo existe o no (spec accounts).
        return back()->with('status', 'Si el correo está registrado, recibirás las instrucciones para restablecer tu contraseña.');
    }
}
