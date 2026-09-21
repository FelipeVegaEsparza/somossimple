@extends('layouts.panel')

@section('title', $promotion ? 'Editar promoción' : 'Nueva promoción')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $promotion ? 'Editar promoción' : 'Nueva promoción' }}</h1>
                <p class="text-sm text-ink-soft mt-1">Se mostrará en la sección de promociones de tu perfil público.</p>
            </div>
            <a href="{{ route('panel.promotions.index') }}" class="btn-ghost">Volver</a>
        </div>

        <form method="POST" action="{{ $promotion ? route('panel.promotions.update', $promotion) : route('panel.promotions.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($promotion)
                @method('PUT')
            @endif

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Información</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="title" class="label">Título</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $promotion?->title) }}" required class="input @error('title') input-error @enderror" placeholder="Ej: 2x1 los martes">
                        @error('title') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="label">Descripción (opcional)</label>
                        <textarea id="description" name="description" rows="3" class="input h-auto py-3">{{ old('description', $promotion?->description) }}</textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="starts_on" class="label">Desde (opcional)</label>
                            <input id="starts_on" type="date" name="starts_on" value="{{ old('starts_on', $promotion?->starts_on?->format('Y-m-d')) }}" class="input @error('starts_on') input-error @enderror">
                            @error('starts_on') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="ends_on" class="label">Hasta (opcional)</label>
                            <input id="ends_on" type="date" name="ends_on" value="{{ old('ends_on', $promotion?->ends_on?->format('Y-m-d')) }}" class="input @error('ends_on') input-error @enderror">
                            @error('ends_on') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="image" class="label">Imagen (opcional)</label>
                        <input id="image" type="file" name="image" accept="image/*" class="input">
                        @error('image') <p class="error-msg">{{ $message }}</p> @enderror
                        @if ($promotion?->image_path)
                            <img src="{{ asset('storage/'.$promotion->image_path) }}" alt="" class="mt-3 w-24 h-24 object-cover rounded-lg border border-line">
                        @endif
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="active" value="1" @checked(old('active', $promotion?->active ?? true)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium">Activa (visible en tu perfil público)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Guardar promoción</button>
            </div>
        </form>
    </div>
@endsection
