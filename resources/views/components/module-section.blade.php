@props(['id' => null, 'icon' => null, 'title' => null])

<section @if ($id) id="{{ $id }}" @endif {{ $attributes->merge(['class' => 'mt-8 border-t border-line pt-6 scroll-mt-4']) }}>
    <div class="flex items-center gap-3">
        @if ($icon)
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-strong text-white shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
            </span>
        @endif
        <h2 class="text-base font-bold tracking-tight text-ink">{{ $title }}</h2>
    </div>

    <div class="mt-4">{{ $slot }}</div>
</section>
