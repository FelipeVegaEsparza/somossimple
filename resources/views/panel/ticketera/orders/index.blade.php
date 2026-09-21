@extends('layouts.panel')

@section('title', 'Órdenes')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Ticketera</p>
        <h1 class="text-3xl font-bold tracking-tight">Órdenes de compra</h1>
    </div>

    @include('panel.ticketera._nav')

    <form method="GET" action="{{ route('panel.ticketera.orders.index') }}" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, correo, teléfono u orden" class="input max-w-sm">
        <select name="event" class="input max-w-xs">
            <option value="">Todos los eventos</option>
            @foreach ($events as $event)
                <option value="{{ $event->id }}" @selected(request('event') == $event->id)>{{ $event->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary shrink-0">Filtrar</button>
    </form>

    @if ($orders->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin órdenes</h2>
            <p class="text-sm text-ink-soft mt-1">Aquí verás las compras de tus eventos.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Orden</th>
                            <th class="table-header px-4 py-3">Comprador</th>
                            <th class="table-header px-4 py-3">Evento</th>
                            <th class="table-header px-4 py-3 text-right">Entradas</th>
                            <th class="table-header px-4 py-3 text-right">Total</th>
                            <th class="table-header px-4 py-3">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-app/60">
                                <td class="px-4 py-3 font-mono text-xs">{{ $order->number }}<span class="block text-ink-faint">{{ $order->created_at->format('d/m/Y H:i') }}</span></td>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $order->buyerFullName() }}</p>
                                    <p class="text-xs text-ink-soft">{{ $order->buyer_email }}</p>
                                </td>
                                <td class="px-4 py-3 text-ink-soft">{{ $order->event?->name }}</td>
                                <td class="px-4 py-3 text-right">{{ $order->tickets_count }}</td>
                                <td class="px-4 py-3 text-right font-semibold">${{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3"><span class="badge {{ $order->status()->badge() }}">{{ $order->status()->label() }}</span></td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('panel.ticketera.orders.show', $order) }}" class="text-sm font-semibold text-primary">Ver</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
@endsection
