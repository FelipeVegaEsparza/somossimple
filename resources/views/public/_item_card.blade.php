@php($precio = $item->priceDisplay())

<article class="flex gap-3 rounded-xl border border-line p-3">
    @if ($item->image_path)
        <img src="{{ asset('storage/'.$item->image_path) }}" alt="" loading="lazy" class="w-16 h-16 object-cover rounded-lg shrink-0">
    @endif
    <div class="flex-1 min-w-0">
        <div class="flex items-baseline justify-between gap-3">
            <h3 class="font-semibold">{{ $item->name }}</h3>
            @if ($precio)
                <p class="text-sm font-semibold text-ink shrink-0">{{ $precio }}</p>
            @endif
        </div>
        @if ($item->description)
            <p class="mt-0.5 text-sm text-ink-soft">{{ $item->description }}</p>
        @endif
    </div>
</article>
