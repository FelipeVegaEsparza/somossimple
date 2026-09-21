@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
    <div class="card p-8">
        <h1 class="text-2xl font-bold tracking-tight">Crear tu cuenta</h1>
        <p class="mt-1 text-sm text-ink-soft">Crea tu negocio y su presencia digital en unos minutos.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="label">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="input @error('name') input-error @enderror">
                @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="label">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="input @error('email') input-error @enderror">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="label">Contraseña</label>
                <input id="password" type="password" name="password" required
                       class="input @error('password') input-error @enderror">
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">Repite la contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="input">
            </div>

            <button type="submit" class="btn-primary w-full">Crear cuenta</button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-ink-soft">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-deep">Inicia sesión</a>
    </p>
@endsection
