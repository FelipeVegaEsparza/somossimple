<x-module-page :business="$business" title="Promociones" :icon="\App\Enums\Module::Promotions->icon()" tagline="Ofertas y descuentos vigentes">
    @if ($promotions->isEmpty())
        <p class="text-sm text-ink-soft">Este negocio no tiene promociones activas por ahora.</p>
    @else
        <div class="space-y-3">
            @foreach ($promotions as $promotion)
                <div class="rounded-xl border border-line overflow-hidden">
                    @if ($promotion->image_path)
                        <img src="{{ asset('storage/'.$promotion->image_path) }}" alt="" loading="lazy" class="w-full h-40 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold">{{ $promotion->title }}</h3>
                        @if ($promotion->description)
                            <p class="mt-1 text-sm text-ink-soft">{{ $promotion->description }}</p>
                        @endif
                        @if ($promotion->validityDisplay())
                            <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-primary">{{ $promotion->validityDisplay() }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-module-page>
