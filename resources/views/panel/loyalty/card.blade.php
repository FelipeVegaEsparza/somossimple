@extends('layouts.panel')

@section('title', 'Tarjeta digital')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Fidelización</p>
        <h1 class="text-3xl font-bold tracking-tight">Tarjeta digital</h1>
        <p class="text-sm text-ink-soft mt-1">Personaliza cómo verá el cliente su tarjeta de fidelización.</p>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <div class="grid lg:grid-cols-2 gap-4 items-start">
        <div class="card p-6">
            <h2 class="text-lg font-semibold">Vista previa</h2>
            <p class="text-sm text-ink-soft mt-0.5">Se actualiza con los colores y textos que elijas.</p>
            <div class="mt-6 flex justify-center">
                @include('loyalty._card', ['business' => $business, 'program' => $program, 'member' => null, 'qrUrl' => null])
            </div>
        </div>

        <form method="POST" action="{{ route('panel.loyalty.card.update') }}" class="card p-6 space-y-4">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold">Diseño</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="card_primary_color" class="label">Color principal</label>
                    <input id="card_primary_color" type="color" name="card_primary_color" value="{{ old('card_primary_color', $program->card_primary_color) }}" class="input h-11 p-1">
                    @error('card_primary_color') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="card_secondary_color" class="label">Color secundario</label>
                    <input id="card_secondary_color" type="color" name="card_secondary_color" value="{{ old('card_secondary_color', $program->card_secondary_color) }}" class="input h-11 p-1">
                    @error('card_secondary_color') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="card_text_primary" class="label">Texto principal</label>
                <input id="card_text_primary" type="text" name="card_text_primary" value="{{ old('card_text_primary', $program->card_text_primary) }}" class="input" placeholder="{{ $business->name }}">
            </div>

            <div>
                <label for="card_text_secondary" class="label">Texto secundario</label>
                <input id="card_text_secondary" type="text" name="card_text_secondary" value="{{ old('card_text_secondary', $program->card_text_secondary) }}" class="input" placeholder="Presenta esta tarjeta en el negocio">
            </div>

            <p class="text-xs text-ink-faint">El logo y la portada se toman del perfil del negocio.</p>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Guardar tarjeta</button>
            </div>
        </form>
    </div>
@endsection
