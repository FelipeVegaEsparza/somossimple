@extends('layouts.panel')

@section('title', 'Recompensas')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Fidelización</p>
            <h1 class="text-3xl font-bold tracking-tight">Recompensas</h1>
            <p class="text-sm text-ink-soft mt-1">Define qué puede obtener el cliente al acumular {{ $program->unit_name }}.</p>
        </div>
        <a href="{{ route('panel.loyalty.rewards.create') }}" class="btn-primary">Nueva recompensa</a>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    @if ($rewards->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no hay recompensas</h2>
            <p class="text-sm text-ink-soft mt-1">Crea la primera, por ejemplo «Café gratis» con 500 puntos.</p>
            <a href="{{ route('panel.loyalty.rewards.create') }}" class="btn-primary mt-5">Crear recompensa</a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($rewards as $reward)
                <div class="card p-5 flex flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-bold">{{ $reward->name }}</h3>
                        <span class="badge {{ $reward->is_active ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $reward->is_active ? 'Activa' : 'Inactiva' }}</span>
                    </div>
                    @if ($reward->description)
                        <p class="mt-1 text-sm text-ink-soft">{{ $reward->description }}</p>
                    @endif
                    <p class="mt-3 text-sm font-semibold">{{ $reward->requirementDisplay() }}</p>
                    @if ($reward->valid_until)
                        <p class="text-xs text-ink-faint mt-0.5">Vigente hasta {{ $reward->valid_until->format('d/m/Y') }}</p>
                    @endif

                    <div class="mt-4 flex items-center gap-1 border-t border-line pt-3">
                        <a href="{{ route('panel.loyalty.rewards.edit', $reward) }}" class="btn-ghost">Editar</a>
                        <form method="POST" action="{{ route('panel.loyalty.rewards.toggle', $reward) }}">
                            @csrf
                            <button type="submit" class="btn-ghost text-ink-soft">{{ $reward->is_active ? 'Desactivar' : 'Activar' }}</button>
                        </form>
                        <form method="POST" action="{{ route('panel.loyalty.rewards.destroy', $reward) }}" onsubmit="return confirm('¿Eliminar esta recompensa?')" class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost text-danger" aria-label="Eliminar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
