@foreach ($promotions as $promotion)
    @php($expired = $promotion->ends_on && $promotion->ends_on->isPast() && ! $promotion->ends_on->isToday())
    @php($scheduled = $promotion->starts_on && $promotion->starts_on->isFuture())
    <div class="card p-4 mb-3 flex items-center gap-4">
        @if ($promotion->image_path)
            <img src="{{ asset('storage/'.$promotion->image_path) }}" alt="" class="w-14 h-14 object-cover rounded-lg border border-line shrink-0">
        @else
            <span class="w-14 h-14 rounded-lg bg-app shrink-0 inline-flex items-center justify-center text-ink-faint">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
            </span>
        @endif

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold truncate">{{ $promotion->title }}</h3>
                @if (! $promotion->active)
                    <span class="badge bg-app text-ink-soft">Inactiva</span>
                @elseif ($expired)
                    <span class="badge bg-red-50 text-danger">Vencida</span>
                @elseif ($scheduled)
                    <span class="badge bg-[#fff6e0] text-warn">Programada</span>
                @else
                    <span class="badge bg-[#e6f6ee] text-good">Vigente</span>
                @endif
            </div>
            @if ($promotion->description)
                <p class="text-sm text-ink-soft truncate">{{ $promotion->description }}</p>
            @endif
            @if ($promotion->validityDisplay())
                <p class="text-sm text-ink-faint mt-0.5">{{ $promotion->validityDisplay() }}</p>
            @endif
        </div>

        <div class="flex items-center gap-1 shrink-0">
            <form method="POST" action="{{ route('panel.promotions.toggle', $promotion) }}">
                @csrf
                <button type="submit" class="btn-ghost text-ink-soft">{{ $promotion->active ? 'Desactivar' : 'Activar' }}</button>
            </form>
            <a href="{{ route('panel.promotions.edit', $promotion) }}" class="btn-ghost">Editar</a>
            <form method="POST" action="{{ route('panel.promotions.destroy', $promotion) }}" onsubmit="return confirm('¿Eliminar esta promoción?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost text-danger" aria-label="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
        </div>
    </div>
@endforeach
