<x-module-page :business="$business" title="Servicios" :icon="\App\Enums\Module::Services->icon()" tagline="Qué hacemos y cuánto cuesta">
    @if ($services->isEmpty())
        <p class="text-sm text-ink-soft">Este negocio aún no publica sus servicios.</p>
    @else
        <div class="space-y-2.5">
            @foreach ($services as $service)
                <article class="rounded-xl border border-line p-3">
                    <div class="flex gap-3">
                        @if ($service->image_path)
                            <img src="{{ asset('storage/'.$service->image_path) }}" alt="" loading="lazy" class="w-16 h-16 object-cover rounded-lg shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="font-semibold">{{ $service->name }}</h3>
                                @if ($service->priceDisplay())
                                    <p class="text-sm font-semibold text-ink shrink-0">{{ $service->priceDisplay() }}</p>
                                @endif
                            </div>
                            @if ($service->description)
                                <p class="mt-0.5 text-sm text-ink-soft">{{ $service->description }}</p>
                            @endif
                            @if ($service->durationDisplay())
                                <p class="mt-1 text-xs text-ink-faint">Duración: {{ $service->durationDisplay() }}</p>
                            @endif
                        </div>
                    </div>
                    @if ($reservationsActive)
                        <a href="{{ route('p.reservation', $business->slug) }}" class="mt-3 flex items-center justify-center rounded-lg border border-line h-10 text-sm font-semibold text-primary transition-colors hover:border-primary/40">Reservar</a>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</x-module-page>
