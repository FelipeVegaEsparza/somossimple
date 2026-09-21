@extends('layouts.panel')

@section('title', 'Entradas')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Ticketera</p>
        <h1 class="text-3xl font-bold tracking-tight">Entradas emitidas</h1>
        <p class="text-sm text-ink-soft mt-1">Cada entrada tiene su propio QR y estado.</p>
    </div>

    @include('panel.ticketera._nav')

    <form method="GET" action="{{ route('panel.ticketera.tickets.index') }}" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por número, token, titular o correo" class="input max-w-sm">
        <select name="event" class="input max-w-xs">
            <option value="">Todos los eventos</option>
            @foreach ($events as $event)
                <option value="{{ $event->id }}" @selected(request('event') == $event->id)>{{ $event->name }}</option>
            @endforeach
        </select>
        <select name="status" class="input w-40">
            <option value="">Todos los estados</option>
            @foreach (['issued' => 'Emitida', 'used' => 'Utilizada', 'cancelled' => 'Cancelada', 'refunded' => 'Reembolsada'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary shrink-0">Filtrar</button>
    </form>

    @if ($tickets->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin entradas</h2>
            <p class="text-sm text-ink-soft mt-1">Se generan automáticamente al vender.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Número</th>
                            <th class="table-header px-4 py-3">Titular</th>
                            <th class="table-header px-4 py-3">Tipo</th>
                            <th class="table-header px-4 py-3">Evento</th>
                            <th class="table-header px-4 py-3 text-right">Precio</th>
                            <th class="table-header px-4 py-3">Estado</th>
                            <th class="table-header px-4 py-3">Ingreso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($tickets as $ticket)
                            <tr class="hover:bg-app/60">
                                <td class="px-4 py-3 font-mono text-xs"><a href="{{ route('panel.ticketera.tickets.show', $ticket) }}" class="text-primary">{{ $ticket->number }}</a></td>
                                <td class="px-4 py-3">{{ $ticket->holder_name }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ $ticket->ticketType?->name }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ $ticket->event?->name }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($ticket->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3"><span class="badge {{ $ticket->status()->badge() }}">{{ $ticket->status()->label() }}</span></td>
                                <td class="px-4 py-3 text-ink-soft">{{ $ticket->used_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $tickets->links() }}</div>
    @endif
@endsection
