<li class="py-3 flex gap-3">
    @if ($item->image_path)
        <img src="{{ asset('storage/'.$item->image_path) }}" alt="" loading="lazy" class="w-16 h-16 object-cover rounded-lg shrink-0">
    @endif
    <div class="flex-1 min-w-0">
        <div class="flex items-baseline justify-between gap-3">
            <h4 class="font-semibold">{{ $item->name }}</h4>
            <p class="text-sm font-semibold text-ink shrink-0">{{ $item->priceDisplay() }}</p>
        </div>
        @if ($item->description)
            <p class="mt-0.5 text-sm text-ink-soft">{{ $item->description }}</p>
        @endif
    </div>
</li>
