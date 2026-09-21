@extends('layouts.panel')

@section('title', 'Galería')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Galería</h1>
            <p class="text-sm text-ink-soft mt-1">Muestra tu negocio en imágenes: trabajos, productos, espacios y resultados.</p>
        </div>
    </div>

    <div class="card p-6 mb-6">
        <h2 class="text-lg font-semibold">Agregar imágenes</h2>
        <p class="text-sm text-ink-soft mt-1">Puedes seleccionar varias a la vez. Se mostrarán en la galería de tu perfil público.</p>

        <form method="POST" action="{{ route('panel.gallery.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <input type="file" name="images[]" accept="image/*" multiple required class="input @error('images') input-error @enderror @error('images.*') input-error @enderror">
            @error('images') <p class="error-msg">{{ $message }}</p> @enderror
            @error('images.*') <p class="error-msg">{{ $message }}</p> @enderror
            <button type="submit" class="btn-primary">Subir imágenes</button>
        </form>
    </div>

    @if ($images->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes imágenes</h2>
            <p class="text-sm text-ink-soft mt-1">Sube tus primeras fotos para dar vida a tu perfil.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach ($images as $image)
                <div class="relative group rounded-xl overflow-hidden border border-line bg-app">
                    <img src="{{ asset('storage/'.$image->path) }}" alt="" loading="lazy" class="w-full h-40 object-cover">
                    <form method="POST" action="{{ route('panel.gallery.destroy', $image) }}" class="absolute top-2 right-2" onsubmit="return confirm('¿Eliminar esta imagen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/90 text-danger shadow-soft hover:bg-white" aria-label="Eliminar imagen">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection
