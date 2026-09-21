<x-module-page :business="$business" title="Catálogo" :icon="\App\Enums\Module::Catalog->icon()" tagline="Nuestra oferta disponible">
    @if ($categories->isEmpty() && $uncategorized->isEmpty())
        <p class="text-sm text-ink-soft">Este negocio aún no publica su catálogo.</p>
    @else
        <div class="space-y-5">
            @foreach ($categories as $category)
                <div>
                    <p class="text-[13px] font-bold uppercase tracking-wide text-ink-faint">{{ $category->name }}</p>
                    <div class="mt-2 space-y-2.5">
                        @foreach ($category->items as $item)
                            @include('public._item_card', ['item' => $item])
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if ($uncategorized->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach ($uncategorized as $item)
                        @include('public._item_card', ['item' => $item])
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</x-module-page>
