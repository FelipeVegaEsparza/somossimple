<x-module-page :business="$business" title="Eventos" :icon="\App\Enums\Module::Events->icon()" tagline="Próximas actividades">
    @if ($events->isEmpty())
        <p class="text-sm text-ink-soft">Este negocio no tiene eventos próximos por ahora.</p>
    @else
        <div class="space-y-3">
            @foreach ($events as $event)
                <div class="rounded-xl border border-line p-4 flex gap-3">
                    @if ($event->image_path)
                        <img src="{{ asset('storage/'.$event->image_path) }}" alt="" loading="lazy" class="w-16 h-16 object-cover rounded-lg shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wide text-primary">{{ $event->dateDisplay() }}</p>
                        <h3 class="mt-0.5 font-semibold">{{ $event->title }}</h3>
                        @if ($event->location)
                            <p class="text-sm text-ink-soft">{{ $event->location }}</p>
                        @endif
                        @if ($event->description)
                            <p class="mt-1 text-sm text-ink-soft">{{ $event->description }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-module-page>
