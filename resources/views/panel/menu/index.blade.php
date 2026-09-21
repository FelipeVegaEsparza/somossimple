@extends('layouts.panel')

@section('title', 'Menú')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Menú</h1>
            <p class="text-sm text-ink-soft mt-1">Tu carta digital, siempre actualizada y accesible desde un QR, NFC o tu Perfil Digital.</p>
        </div>
        <a href="{{ route('panel.menu.create') }}" class="btn-primary">Nuevo plato</a>
    </div>

    <div class="card p-5 mb-4">
        <h2 class="font-semibold text-sm">Categorías</h2>
        <p class="text-sm text-ink-soft">Agrupa tu carta: entradas, platos de fondo, postres, bebidas…</p>

        <form method="POST" action="{{ route('panel.menu.category.store') }}" class="mt-4 flex gap-3 max-w-md">
            @csrf
            <input type="text" name="name" placeholder="Nueva categoría, ej: Platos de fondo" class="input" required>
            <button type="submit" class="btn-secondary shrink-0">Agregar</button>
        </form>

        @if ($categories->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($categories as $category)
                    <span class="inline-flex items-center gap-2 rounded-lg bg-app px-3 h-9 text-sm font-medium">
                        {{ $category->name }}
                        <span class="text-ink-faint">· {{ $category->items_count }}</span>
                        <form method="POST" action="{{ route('panel.menu.category.destroy', $category) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-ink-faint hover:text-danger" aria-label="Eliminar categoría">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    @php($hasItems = $itemsByCategory->contains(fn ($c) => $c->items->isNotEmpty()) || $uncategorizedItems->isNotEmpty())

    @if (! $hasItems)
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes platos</h2>
            <p class="text-sm text-ink-soft mt-1">Agrega tu primer plato o producto de la carta.</p>
            <a href="{{ route('panel.menu.create') }}" class="btn-primary mt-5">Crear plato</a>
        </div>
    @else
        @foreach ($itemsByCategory as $category)
            @if ($category->items->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">{{ $category->name }}</h2>
                    @include('panel.menu._list', ['items' => $category->items])
                </div>
            @endif
        @endforeach

        @if ($uncategorizedItems->isNotEmpty())
            <h2 class="text-lg font-semibold mb-3">Sin categoría</h2>
            @include('panel.menu._list', ['items' => $uncategorizedItems])
        @endif
    @endif
@endsection
