@extends('layouts.panel')

@section('title', 'Registrar cliente')

@section('content')
    <div class="max-w-xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Registrar cliente</h1>
                <p class="text-sm text-ink-soft mt-1">Crea un cliente directamente en tu base.</p>
            </div>
            <a href="{{ route('panel.clients.index') }}" class="btn-ghost">Volver</a>
        </div>

        <div class="card p-6">
            <form method="POST" action="{{ route('panel.clients.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="label">Nombre</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input">
                    @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="label">Teléfono / WhatsApp</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="input" placeholder="+56 9 …">
                </div>

                <div>
                    <label for="email" class="label">Correo</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="input">
                    @error('email') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Registrar cliente</button>
                </div>
            </form>
        </div>
    </div>
@endsection
