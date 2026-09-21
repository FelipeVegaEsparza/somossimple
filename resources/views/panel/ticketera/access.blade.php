@extends('layouts.panel')

@section('title', 'Control de acceso')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Ticketera</p>
        <h1 class="text-3xl font-bold tracking-tight">Control de acceso</h1>
        <p class="text-sm text-ink-soft mt-1">Escanea el QR de cada entrada. Vuelve solo al escáner tras cada validación.</p>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    @if ($events->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">No hay eventos activos</h2>
            <p class="text-sm text-ink-soft mt-1">Publica un evento para habilitar el control de acceso.</p>
        </div>
    @else
        <div class="max-w-md">
            <form method="GET" action="{{ route('panel.ticketera.access') }}" class="mb-4">
                <label class="label">Evento</label>
                <select name="event" class="input" onchange="this.form.submit()">
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" @selected($selected?->id === $event->id)>{{ $event->name }}</option>
                    @endforeach
                </select>
            </form>

            @if ($selected)
                <x-ticket-scanner
                    :endpoint="route('panel.ticketera.access.validate')"
                    :event="$selected->id"
                    :counters="$counters"
                />
            @endif

            @if ($staffLink)
                <div class="card p-4 mt-4">
                    <p class="text-sm font-semibold">Enlace para personal de acceso</p>
                    <p class="text-xs text-ink-soft mt-0.5">Compártelo con quien controla la puerta; no necesita acceso al panel.</p>
                    <p class="mt-2 break-all font-mono text-xs text-primary">{{ $staffLink }}</p>
                </div>
            @else
                <div class="card p-4 mt-4 text-sm text-ink-soft">
                    ¿Necesitas más teléfonos validando? <a href="{{ route('panel.ticketera.staff.index') }}" class="font-semibold text-primary">Agrega personal de acceso</a>.
                </div>
            @endif
        </div>
    @endif
@endsection
