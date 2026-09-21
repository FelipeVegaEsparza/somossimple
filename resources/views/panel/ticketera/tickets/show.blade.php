@extends('layouts.panel')

@section('title', $ticket->number)

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Ticketera · Entradas</p>
        <h1 class="text-3xl font-bold tracking-tight font-mono">{{ $ticket->number }}</h1>
        <p class="mt-1"><span class="badge {{ $ticket->status()->badge() }}">{{ $ticket->status()->label() }}</span></p>
    </div>

    @include('panel.ticketera._nav')

    <div class="grid lg:grid-cols-2 gap-4 items-start">
        <div class="card p-6 flex flex-col items-center text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">{{ $ticket->event?->name }}</p>
            <p class="mt-1 text-lg font-bold">{{ $ticket->ticketType?->name }}</p>
            <div class="mt-4 rounded-2xl border border-line bg-white p-3">
                <img src="{{ route('ticketera.ticket.qr', $ticket->token) }}" alt="QR {{ $ticket->number }}" class="w-52 h-52">
            </div>
            <p class="mt-3 font-mono text-sm text-ink-soft">{{ $ticket->token }}</p>
            @if ($ticket->isUsable())
                <a href="{{ route('ticketera.ticket', $ticket->token) }}" target="_blank" rel="noopener" class="btn-secondary mt-4">Ver entrada pública</a>
            @endif
        </div>

        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Detalle</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-soft">Titular</dt><dd>{{ $ticket->holder_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Evento</dt><dd>{{ $ticket->event?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Tipo</dt><dd>{{ $ticket->ticketType?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Precio</dt><dd>${{ number_format($ticket->price, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Orden</dt><dd>
                        @if ($ticket->order)
                            <a href="{{ route('panel.ticketera.orders.show', $ticket->order) }}" class="text-primary font-mono">{{ $ticket->order->number }}</a>
                        @else — @endif
                    </dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Emitida</dt><dd>{{ $ticket->issued_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Utilizada</dt><dd>{{ $ticket->used_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Control de acceso</h2>
                @if ($ticket->access)
                    <p class="mt-2 text-sm text-ink-soft">Ingresó el {{ $ticket->access->created_at->format('d/m/Y H:i') }} · validado por {{ $ticket->access->user?->name ?? $ticket->access->staff?->name ?? 'Sistema' }}.</p>
                @elseif ($ticket->status()->value === 'issued')
                    <p class="mt-2 text-sm text-ink-soft">Aún no ha ingresado.</p>
                @else
                    <p class="mt-2 text-sm text-ink-soft">Estado actual: {{ $ticket->status()->label() }}.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
