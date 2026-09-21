@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="card p-8">
        <h1 class="text-2xl font-bold tracking-tight">Inicia sesión</h1>
        <p class="mt-1 text-sm text-ink-soft">Administra tu negocio desde tu panel.</p>

        @if (session('status'))
            <div class="mt-5 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="label">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="input @error('email') input-error @enderror">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="label">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="input @error('password') input-error @enderror">
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <label class="flex items-center gap-2.5 text-sm text-ink-soft cursor-pointer">
                    <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-line text-primary accent-primary">
                    Recordarme
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-ink-soft hover:text-ink">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-primary w-full h-12">Entrar</button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-ink-soft">
        ¿No tienes cuenta? <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-deep">Crea tu negocio gratis</a>
    </p>
@endsection
