@extends('layouts.panel')

@section('title', $item ? 'Editar elemento' : 'Nuevo elemento')

@section('content')
    @php($priceMode = old('price_mode', $item?->price_mode ?? 'none'))
    @php($priceValue = old('price', $item?->price))

    <div class="max-w-2xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $item ? 'Editar elemento' : 'Nuevo elemento' }}</h1>
                <p class="text-sm text-ink-soft mt-1">Un solo elemento sirve para productos, servicios o menú.</p>
            </div>
            <a href="{{ route('panel.catalog.index') }}" class="btn-ghost">Volver</a>
        </div>

        <form method="POST" action="{{ $item ? route('panel.catalog.update', $item) : route('panel.catalog.store') }}" enctype="multipart/form-data" class="space-y-4"
              x-data="{ mode: @js($priceMode) }">
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
                            @foreach ($business->catalogCategories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $item?->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Precio</label>
                        <div class="flex gap-2">
                            <label class="flex-1">
                                <input type="radio" name="price_mode" value="none" x-model="mode" class="sr-only peer">
                                <span class="block text-center rounded-lg border border-line px-3 py-2.5 text-sm font-medium peer-checked:border-primary peer-checked:bg-primary-tint peer-checked:text-primary">Consultar precio</span>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="price_mode" value="exact" x-model="mode" class="sr-only peer">
                                <span class="block text-center rounded-lg border border-line px-3 py-2.5 text-sm font-medium peer-checked:border-primary peer-checked:bg-primary-tint peer-checked:text-primary">Precio exacto</span>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="price_mode" value="from" x-model="mode" class="sr-only peer">
                                <span class="block text-center rounded-lg border border-line px-3 py-2.5 text-sm font-medium peer-checked:border-primary peer-checked:bg-primary-tint peer-checked:text-primary">Desde…</span>
                            </label>
                        </div>
                        <input x-show="mode !== 'none'" x-cloak type="number" name="price" value="{{ $priceValue }}" min="0" step="1"
                               class="input mt-3" placeholder="Precio en pesos (CLP)" @error('price') input-error @enderror>
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
                        <input type="checkbox" name="active" value="1" @checked(old('active', $item?->active ?? true)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium">Activo (visible en tu perfil público)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Guardar elemento</button>
            </div>
        </form>
    </div>
@endsection
