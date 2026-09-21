@foreach ($events as $event)
    <div class="card p-4 mb-3 flex items-center gap-4">
        @if ($event->image_path)
            <img src="{{ asset('storage/'.$event->image_path) }}" alt="" class="w-14 h-14 object-cover rounded-lg border border-line shrink-0">
        @else
            <span class="w-14 h-14 rounded-lg bg-app shrink-0 inline-flex items-center justify-center text-ink-faint">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            </span>
        @endif

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold truncate">{{ $event->title }}</h3>
                @if (! $event->active)
                    <span class="badge bg-app text-ink-soft">Inactivo</span>
                @endif
            </div>
            @if ($event->description)
                <p class="text-sm text-ink-soft truncate">{{ $event->description }}</p>
            @endif
            <p class="text-sm text-ink-faint mt-0.5">
                {{ $event->dateDisplay() }}
                @if ($event->location) · {{ $event->location }} @endif
            </p>
        </div>

        <div class="flex items-center gap-1 shrink-0">
            <form method="POST" action="{{ route('panel.events.toggle', $event) }}">
                @csrf
                <button type="submit" class="btn-ghost text-ink-soft">{{ $event->active ? 'Desactivar' : 'Activar' }}</button>
            </form>
            <a href="{{ route('panel.events.edit', $event) }}" class="btn-ghost">Editar</a>
            <form method="POST" action="{{ route('panel.events.destroy', $event) }}" onsubmit="return confirm('¿Eliminar este evento?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost text-danger" aria-label="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
        </div>
    </div>
@endforeach
