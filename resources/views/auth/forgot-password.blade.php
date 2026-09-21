@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
    <div class="card p-8">
        <h1 class="text-2xl font-bold tracking-tight">Recuperar tu contraseña</h1>
        <p class="mt-1 text-sm text-ink-soft">Te enviaremos un enlace a tu correo para que la restablezcas.</p>

        @if (session('status'))
            <div class="mt-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="label">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="input @error('email') input-error @enderror">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Enviar enlace de recuperación</button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-ink-soft">
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-deep">Volver a iniciar sesión</a>
    </p>
@endsection
