@extends('layouts.panel')

@section('title', 'Ticketera')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Módulo</p>
            <h1 class="text-3xl font-bold tracking-tight">Ticketera</h1>
            <p class="text-sm text-ink-soft mt-1">Crea eventos, vende entradas online y valida el acceso con QR.</p>
        </div>
        <a href="{{ route('panel.ticketera.events.create') }}" class="btn-primary">+ Crear evento</a>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach ([
            ['Eventos activos', $stats['active_events']],
            ['Entradas vendidas', $stats['tickets_sold']],
            ['Ingresos', '$'.number_format($stats['revenue'], 0, ',', '.')],
            ['Entradas disponibles', $stats['available']],
            ['Entradas utilizadas', $stats['used']],
        ] as $card)
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">{{ $card[0] }}</p>
                <p class="mt-2 text-2xl font-extrabold tracking-tight">{{ is_int($card[1]) ? number_format($card[1], 0, ',', '.') : $card[1] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-4 mt-4">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Próximos eventos</h2>
                <a href="{{ route('panel.ticketera.events.index') }}" class="text-sm font-semibold text-primary">Ver todos</a>
            </div>
            @forelse ($upcoming as $event)
                <a href="{{ route('panel.ticketera.events.show', $event) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-line p-3 hover:border-primary/40">
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ $event->name }}</p>
                        <p class="text-xs text-ink-soft">{{ $event->starts_at->translatedFormat('d M Y · H:i') }} · {{ $event->venue_name ?: 'Sin lugar' }}</p>
                    </div>
                    <span class="badge {{ $event->status()->badge() }} shrink-0">{{ $event->status()->label() }}</span>
                </a>
            @empty
                <p class="mt-3 text-sm text-ink-soft">No hay eventos publicados próximos.</p>
            @endforelse
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Ventas recientes</h2>
                <a href="{{ route('panel.ticketera.orders.index') }}" class="text-sm font-semibold text-primary">Ver todas</a>
            </div>
            @forelse ($recentOrders as $order)
                <a href="{{ route('panel.ticketera.orders.show', $order) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-line p-3 hover:border-primary/40">
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ $order->buyerFullName() }}</p>
                        <p class="text-xs text-ink-soft font-mono">{{ $order->number }} · {{ $order->event?->name }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-semibold">${{ number_format($order->total, 0, ',', '.') }}</p>
                        <span class="badge {{ $order->status()->badge() }}">{{ $order->status()->label() }}</span>
                    </div>
                </a>
            @empty
                <p class="mt-3 text-sm text-ink-soft">Todavía no hay ventas.</p>
            @endforelse
        </div>
    </div>
@endsection
