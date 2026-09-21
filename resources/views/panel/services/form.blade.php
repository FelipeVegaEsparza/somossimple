@extends('layouts.panel')

@section('title', $service ? 'Editar servicio' : 'Nuevo servicio')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $service ? 'Editar servicio' : 'Nuevo servicio' }}</h1>
                <p class="text-sm text-ink-soft mt-1">Aparecerá en la sección de servicios de tu perfil público.</p>
            </div>
            <a href="{{ route('panel.services.index') }}" class="btn-ghost">Volver</a>
        </div>

        <form method="POST" action="{{ $service ? route('panel.services.update', $service) : route('panel.services.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($service)
                @method('PUT')
            @endif

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Información</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="name" class="label">Nombre</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $service?->name) }}" required class="input @error('name') input-error @enderror">
                        @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="label">Descripción (opcional)</label>
                        <textarea id="description" name="description" rows="3" class="input h-auto py-3">{{ old('description', $service?->description) }}</textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="label">Precio (opcional)</label>
                            <input id="price" type="number" name="price" value="{{ old('price', $service?->price) }}" min="0" step="1"
                                   class="input @error('price') input-error @enderror" placeholder="Precio en pesos (CLP)">
                            @error('price') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="duration_minutes" class="label">Duración en minutos (opcional)</label>
                            <input id="duration_minutes" type="number" name="duration_minutes" value="{{ old('duration_minutes', $service?->duration_minutes) }}" min="1" max="1440" step="1"
                                   class="input @error('duration_minutes') input-error @enderror" placeholder="Ej: 45">
                            @error('duration_minutes') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="image" class="label">Imagen (opcional)</label>
                        <input id="image" type="file" name="image" accept="image/*" class="input">
                        @error('image') <p class="error-msg">{{ $message }}</p> @enderror
                        @if ($service?->image_path)
                            <img src="{{ asset('storage/'.$service->image_path) }}" alt="" class="mt-3 w-24 h-24 object-cover rounded-lg border border-line">
                        @endif
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="active" value="1" @checked(old('active', $service?->active ?? true)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium">Activo (visible en tu perfil público)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Guardar servicio</button>
            </div>
        </form>
    </div>
@endsection
