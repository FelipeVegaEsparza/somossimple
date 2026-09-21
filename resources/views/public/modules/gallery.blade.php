<x-module-page :business="$business" title="Galería" :icon="\App\Enums\Module::Gallery->icon()" tagline="Así es nuestro negocio">
    @if ($images->isEmpty())
        <p class="text-sm text-ink-soft">Este negocio aún no publica imágenes.</p>
    @else
        <div class="grid grid-cols-2 gap-2">
            @foreach ($images as $image)
                <img src="{{ asset('storage/'.$image->path) }}" alt="" loading="lazy" class="w-full h-36 object-cover rounded-xl border border-line">
            @endforeach
        </div>
    @endif
</x-module-page>
