@extends('layouts.panel')

@section('title', $order->number)

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Ticketera · Órdenes</p>
            <h1 class="text-3xl font-bold tracking-tight font-mono">{{ $order->number }}</h1>
            <p class="mt-1 flex items-center gap-2 text-sm text-ink-soft">
                {{ $order->created_at->format('d/m/Y H:i') }}
                <span class="badge {{ $order->status()->badge() }}">{{ $order->status()->label() }}</span>
            </p>
        </div>
        <a href="{{ route('panel.ticketera.orders.index') }}" class="btn-ghost">← Volver</a>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-4 items-start">
        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Comprador</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-soft">Nombre</dt><dd>{{ $order->buyerFullName() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Correo</dt><dd>{{ $order->buyer_email }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Teléfono</dt><dd>{{ $order->buyer_phone ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Evento</dt><dd>{{ $order->event?->name }}</dd></div>
                </dl>
                <div class="mt-4 border-t border-line pt-4 text-sm">
                    <div class="flex justify-between"><span class="text-ink-soft">Subtotal</span><span>${{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-soft">Comisión</span><span>${{ number_format($order->commission, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-semibold"><span>Total</span><span>${{ number_format($order->total, 0, ',', '.') }}</span></div>
                </div>
                <p class="mt-3 text-xs text-ink-faint">Método de pago: {{ $order->payment_provider ?: '—' }} @if ($order->payment_reference) · Ref: {{ $order->payment_reference }} @endif</p>
            </div>

            @if ($order->status()->value !== 'refunded')
                <div class="card p-6">
                    <h2 class="text-lg font-semibold">Reembolsar</h2>
                    <p class="text-sm text-ink-soft mt-0.5">Al reembolsar, las entradas quedan invalidadas y no permitirán el ingreso.</p>
                    <form method="POST" action="{{ route('panel.ticketera.orders.refund', $order) }}" class="mt-3 space-y-3" onsubmit="return confirm('¿Reembolsar esta orden? Las entradas quedarán invalidadas.')">
                        @csrf
                        <input type="text" name="reason" class="input" placeholder="Motivo (opcional)">
                        <button type="submit" class="btn-secondary border-danger/40 text-danger">Reembolsar orden</button>
                    </form>
                </div>
            @endif

            @if ($order->refunds->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-semibold">Reembolsos</h2>
                    <ul class="mt-3 divide-y divide-line text-sm">
                        @foreach ($order->refunds as $refund)
                            <li class="py-2 flex justify-between"><span>{{ $refund->refunded_at?->format('d/m/Y H:i') }} · {{ $refund->reason ?: 'Sin motivo' }}</span><span class="font-semibold">${{ number_format($refund->amount, 0, ',', '.') }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Entradas ({{ $order->tickets->count() }})</h2>
            <ul class="mt-3 divide-y divide-line">
                @foreach ($order->tickets as $ticket)
                    <li class="py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('panel.ticketera.tickets.show', $ticket) }}" class="font-semibold text-primary font-mono">{{ $ticket->number }}</a>
                            <p class="text-xs text-ink-soft">{{ $ticket->ticketType?->name }} · {{ $ticket->holder_name }}</p>
                        </div>
                        <span class="badge {{ $ticket->status()->badge() }} shrink-0">{{ $ticket->status()->label() }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
