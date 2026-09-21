@extends('layouts.panel')

@section('title', $reward ? 'Editar recompensa' : 'Nueva recompensa')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Fidelización · Recompensas</p>
        <h1 class="text-3xl font-bold tracking-tight">{{ $reward ? 'Editar recompensa' : 'Nueva recompensa' }}</h1>
    </div>

    @include('panel.loyalty._nav')

    <form method="POST" action="{{ $reward ? route('panel.loyalty.rewards.update', $reward) : route('panel.loyalty.rewards.store') }}" enctype="multipart/form-data" class="max-w-xl card p-6 space-y-4">
        @csrf
        @if ($reward)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="label">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name', $reward?->name) }}" required class="input @error('name') input-error @enderror" placeholder="Café gratis">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="label">Descripción (opcional)</label>
            <textarea id="description" name="description" rows="2" class="input h-auto py-3">{{ old('description', $reward?->description) }}</textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="requirement_type" class="label">Requisito</label>
                <select id="requirement_type" name="requirement_type" class="input">
                    @foreach (['points' => 'Puntos', 'visits' => 'Visitas', 'stamps' => 'Sellos'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('requirement_type', $reward?->requirement_type?->value ?? 'points') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="requirement_units" class="label">Cantidad requerida</label>
                <input id="requirement_units" type="number" name="requirement_units" min="1" value="{{ old('requirement_units', $reward?->requirement_units ?? 500) }}" required class="input @error('requirement_units') input-error @enderror">
                @error('requirement_units') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="valid_until" class="label">Vigencia hasta (opcional)</label>
            <input id="valid_until" type="date" name="valid_until" value="{{ old('valid_until', $reward?->valid_until?->format('Y-m-d')) }}" class="input">
        </div>

        <div>
            <label for="image" class="label">Imagen (opcional)</label>
            <input id="image" type="file" name="image" accept="image/*" class="input">
            @error('image') <p class="error-msg">{{ $message }}</p> @enderror
            @if ($reward?->image_path)
                <img src="{{ asset('storage/'.$reward->image_path) }}" alt="" class="mt-3 w-24 h-24 object-cover rounded-lg border border-line">
            @endif
        </div>

        <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $reward?->is_active ?? true)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
            <span class="text-sm font-medium">Recompensa activa</span>
        </label>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('panel.loyalty.rewards.index') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" class="btn-primary">Guardar recompensa</button>
        </div>
    </form>
@endsection
