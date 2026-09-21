@extends('layouts.panel')

@section('title', $item ? 'Editar plato' : 'Nuevo plato')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $item ? 'Editar plato' : 'Nuevo plato' }}</h1>
                <p class="text-sm text-ink-soft mt-1">Aparecerá en tu menú digital dentro del perfil público.</p>
            </div>
            <a href="{{ route('panel.menu.index') }}" class="btn-ghost">Volver</a>
        </div>

        <form method="POST" action="{{ $item ? route('panel.menu.update', $item) : route('panel.menu.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($item)
                @method('PUT')
            @endif

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Información</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="name" class="label">Nombre</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $item?->name) }}" required class="input @error('name') input-error @enderror">
                        @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="label">Descripción (opcional)</label>
                        <textarea id="description" name="description" rows="3" class="input h-auto py-3">{{ old('description', $item?->description) }}</textarea>
                    </div>

                    <div>
                        <label for="category_id" class="label">Categoría (opcional)</label>
                        <select id="category_id" name="category_id" class="input">
                            <option value="">Sin categoría</option>
                            @foreach ($business->menuCategories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $item?->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="price" class="label">Precio (opcional)</label>
                        <input id="price" type="number" name="price" value="{{ old('price', $item?->price) }}" min="0" step="1"
                               class="input @error('price') input-error @enderror" placeholder="Precio en pesos (CLP)">
                        @error('price') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="image" class="label">Imagen (opcional)</label>
                        <input id="image" type="file" name="image" accept="image/*" class="input">
                        @error('image') <p class="error-msg">{{ $message }}</p> @enderror
                        @if ($item?->image_path)
                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="" class="mt-3 w-24 h-24 object-cover rounded-lg border border-line">
                        @endif
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="featured" value="1" @checked(old('featured', $item?->featured)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium">Destacado (aparece primero en el menú)</span>
                    </label>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="active" value="1" @checked(old('active', $item?->active ?? true)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium">Activo (visible en tu perfil público)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Guardar plato</button>
            </div>
        </form>
    </div>
@endsection
