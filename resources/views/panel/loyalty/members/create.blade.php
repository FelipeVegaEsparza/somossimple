@extends('layouts.panel')

@section('title', 'Nuevo cliente de fidelización')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Fidelización · Clientes</p>
        <h1 class="text-3xl font-bold tracking-tight">Nuevo cliente</h1>
        <p class="text-sm text-ink-soft mt-1">Al guardarlo se genera su identificador único (CLI-000001) y su código QR.</p>
    </div>

    @include('panel.loyalty._nav')

    <form method="POST" action="{{ route('panel.loyalty.members.store') }}" class="max-w-xl card p-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="label">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input @error('name') input-error @enderror">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="phone" class="label">Teléfono / WhatsApp</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="input" placeholder="+56 9 …">
            </div>
            <div>
                <label for="email" class="label">Correo (opcional)</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="input">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('panel.loyalty.members.index') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" class="btn-primary">Registrar cliente</button>
        </div>
    </form>
@endsection
