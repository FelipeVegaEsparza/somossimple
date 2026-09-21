@extends('layouts.panel')

@section('title', $event ? 'Editar evento' : 'Nuevo evento')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $event ? 'Editar evento' : 'Nuevo evento' }}</h1>
                <p class="text-sm text-ink-soft mt-1">Aparecerá en la sección de eventos de tu perfil público.</p>
            </div>
            <a href="{{ route('panel.events.index') }}" class="btn-ghost">Volver</a>
        </div>

        <form method="POST" action="{{ $event ? route('panel.events.update', $event) : route('panel.events.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($event)
                @method('PUT')
            @endif

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Información</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="title" class="label">Título</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $event?->title) }}" required class="input @error('title') input-error @enderror" placeholder="Ej: Taller de fotografía">
                        @error('title') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="label">Descripción (opcional)</label>
                        <textarea id="description" name="description" rows="3" class="input h-auto py-3">{{ old('description', $event?->description) }}</textarea>
                    </div>

                    <div>
                        <label for="location" class="label">Lugar (opcional)</label>
                        <input id="location" type="text" name="location" value="{{ old('location', $event?->location) }}" class="input" placeholder="Ej: Av. Principal 123">
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="starts_at" class="label">Inicio</label>
                            <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d\TH:i')) }}" required class="input @error('starts_at') input-error @enderror">
                            @error('starts_at') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="ends_at" class="label">Término (opcional)</label>
                            <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $event?->ends_at?->format('Y-m-d\TH:i')) }}" class="input @error('ends_at') input-error @enderror">
                            @error('ends_at') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="image" class="label">Imagen (opcional)</label>
                        <input id="image" type="file" name="image" accept="image/*" class="input">
                        @error('image') <p class="error-msg">{{ $message }}</p> @enderror
                        @if ($event?->image_path)
                            <img src="{{ asset('storage/'.$event->image_path) }}" alt="" class="mt-3 w-24 h-24 object-cover rounded-lg border border-line">
                        @endif
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="active" value="1" @checked(old('active', $event?->active ?? true)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium">Activo (visible en tu perfil público)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Guardar evento</button>
            </div>
        </form>
    </div>
@endsection
