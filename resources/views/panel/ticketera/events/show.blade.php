@extends('layouts.panel')

@section('title', $event->name)

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Ticketera · Eventos</p>
            <h1 class="text-3xl font-bold tracking-tight">{{ $event->name }}</h1>
            <p class="mt-1 flex items-center gap-2 text-sm text-ink-soft">
                {{ $event->starts_at->translatedFormat('d M Y · H:i') }}
                <span class="badge {{ $event->status()->badge() }}">{{ $event->status()->label() }}</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('ticketera.event', $event->slug) }}" target="_blank" rel="noopener" class="btn-secondary">Ver página pública</a>
            <a href="{{ route('panel.ticketera.events.edit', $event) }}" class="btn-secondary">Editar</a>
        </div>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    @if ($event->isCancelled())
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">
            <strong>EVENTO CANCELADO.</strong> Las entradas asociadas quedaron invalidadas. Prepara la devolución y notificación a los compradores.
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach ([
            ['Vendidas', $counters['total'] - $counters['pending']],
            ['Recaudación', '$'.number_format((int) $event->orders()->where('status', 'paid')->sum('total'), 0, ',', '.')],
            ['Disponibles', $event->availableTickets()],
            ['Accesos', $counters['entered']],
            ['Pendientes', $counters['pending']],
        ] as $card)
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">{{ $card[0] }}</p>
                <p class="mt-2 text-2xl font-extrabold tracking-tight">{{ is_int($card[1]) ? number_format($card[1], 0, ',', '.') : $card[1] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-4 mt-4 items-start">
        <div class="card p-6">
            <h2 class="text-lg font-semibold">Ventas por tipo de entrada</h2>
            @if ($event->ticketTypes->isEmpty())
                <p class="mt-3 text-sm text-ink-soft">Aún no hay tipos de entrada.</p>
            @else
                <ul class="mt-4 divide-y divide-line">
                    @foreach ($event->ticketTypes as $type)
                        <li class="py-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium">{{ $type->name }}</p>
                                <p class="text-xs text-ink-soft">{{ $type->priceDisplay() }} · {{ $type->remaining() }} disponibles</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">{{ $type->sold }} vendidas</p>
                                <p class="text-xs text-ink-soft">de {{ $type->stock }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Herramientas</h2>
            <div class="mt-4 space-y-2">
                <a href="{{ route('panel.ticketera.orders.index', ['event' => $event->id]) }}" class="btn-secondary w-full">Ver órdenes del evento</a>
                <a href="{{ route('panel.ticketera.tickets.index', ['event' => $event->id]) }}" class="btn-secondary w-full">Ver entradas del evento</a>
                <a href="{{ route('panel.ticketera.export.orders', $event) }}" class="btn-secondary w-full">Exportar órdenes (CSV)</a>
                <a href="{{ route('panel.ticketera.export.tickets', $event) }}" class="btn-secondary w-full">Exportar entradas (CSV)</a>
                <a href="{{ route('panel.ticketera.export.accesses', $event) }}" class="btn-secondary w-full">Exportar accesos (CSV)</a>
            </div>
        </div>
    </div>
@endsection
