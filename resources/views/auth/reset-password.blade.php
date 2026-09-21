@extends('layouts.guest')

@section('title', 'Nueva contraseña')

@section('content')
    <div class="card p-8">
        <h1 class="text-2xl font-bold tracking-tight">Define una contraseña nueva</h1>
        <p class="mt-1 text-sm text-ink-soft">Elige una contraseña para tu cuenta.</p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="label">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                       class="input @error('email') input-error @enderror">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="label">Contraseña nueva</label>
                <input id="password" type="password" name="password" required
                       class="input @error('password') input-error @enderror">
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">Repite la contraseña nueva</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="input">
            </div>

            <button type="submit" class="btn-primary w-full">Restablecer contraseña</button>
        </form>
    </div>
@endsection
