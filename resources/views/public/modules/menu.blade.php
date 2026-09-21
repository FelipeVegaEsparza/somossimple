<x-module-page :business="$business" title="Menú" :icon="\App\Enums\Module::Menu->icon()" tagline="Nuestra carta, siempre actualizada">
    @if ($featured->isEmpty() && $categories->isEmpty() && $uncategorized->isEmpty())
        <p class="text-sm text-ink-soft">Este negocio aún no publica su menú.</p>
    @else
        <div class="space-y-5">
            @if ($featured->isNotEmpty())
                <div>
                    <p class="text-[13px] font-bold uppercase tracking-wide text-primary">Destacados</p>
                    <div class="mt-2 space-y-2.5">
                        @foreach ($featured as $item)
                            @include('public._item_card', ['item' => $item])
                        @endforeach
                    </div>
                </div>
            @endif

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
