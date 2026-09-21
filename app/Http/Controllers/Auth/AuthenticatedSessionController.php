<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Ingresa tu correo.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ])->onlyInput('email');
        }

        if (auth()->user()->business?->is_paused) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tu cuenta está pausada. Contacta al administrador de la plataforma.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (auth()->user()->is_platform_admin) {
            return redirect()->route('admin.index');
        }

        return redirect()->intended(route('panel.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
