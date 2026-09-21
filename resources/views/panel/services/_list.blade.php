@foreach ($services as $service)
    <div class="card p-4 mb-3 flex items-center gap-4">
        @if ($service->image_path)
            <img src="{{ asset('storage/'.$service->image_path) }}" alt="" class="w-14 h-14 object-cover rounded-lg border border-line shrink-0">
        @else
            <span class="w-14 h-14 rounded-lg bg-app shrink-0 inline-flex items-center justify-center text-ink-faint">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
            </span>
        @endif

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold truncate">{{ $service->name }}</h3>
                @if (! $service->active)
                    <span class="badge bg-app text-ink-soft">Inactivo</span>
                @endif
            </div>
            @if ($service->description)
                <p class="text-sm text-ink-soft truncate">{{ $service->description }}</p>
            @endif
            <p class="text-sm font-semibold mt-0.5">
                {{ $service->priceDisplay() ?? 'Precio a consultar' }}
                @if ($service->durationDisplay())
                    <span class="text-ink-faint font-normal">· {{ $service->durationDisplay() }}</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-1 shrink-0">
            <form method="POST" action="{{ route('panel.services.toggle', $service) }}">
                @csrf
                <button type="submit" class="btn-ghost text-ink-soft" title="{{ $service->active ? 'Desactivar' : 'Activar' }}">
                    {{ $service->active ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            <a href="{{ route('panel.services.edit', $service) }}" class="btn-ghost">Editar</a>
            <form method="POST" action="{{ route('panel.services.destroy', $service) }}" onsubmit="return confirm('¿Eliminar este servicio?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost text-danger" aria-label="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
        </div>
    </div>
@endforeach
