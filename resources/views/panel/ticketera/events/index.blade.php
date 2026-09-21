@extends('layouts.panel')

@section('title', 'Eventos')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Ticketera</p>
            <h1 class="text-3xl font-bold tracking-tight">Eventos</h1>
        </div>
        <a href="{{ route('panel.ticketera.events.create') }}" class="btn-primary">+ Crear evento</a>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('panel.ticketera.events.index') }}" class="mb-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar evento" class="input">
        <button type="submit" class="btn-secondary shrink-0">Buscar</button>
    </form>

    @if ($events->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes eventos</h2>
            <p class="text-sm text-ink-soft mt-1">Crea tu primer evento, define las entradas y publícalo.</p>
            <a href="{{ route('panel.ticketera.events.create') }}" class="btn-primary mt-5">Crear evento</a>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Evento</th>
                            <th class="table-header px-4 py-3">Fecha</th>
                            <th class="table-header px-4 py-3 text-right">Entradas</th>
                            <th class="table-header px-4 py-3 text-right">Órdenes</th>
                            <th class="table-header px-4 py-3">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($events as $event)
                            <tr class="hover:bg-app/60">
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $event->name }}</p>
                                    <p class="text-xs text-ink-soft">{{ $event->venue_name ?: 'Sin lugar' }}</p>
                                </td>
                                <td class="px-4 py-3 text-ink-soft whitespace-nowrap">{{ $event->starts_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">{{ $event->tickets_count }}</td>
                                <td class="px-4 py-3 text-right">{{ $event->orders_count }}</td>
                                <td class="px-4 py-3"><span class="badge {{ $event->status()->badge() }}">{{ $event->status()->label() }}</span></td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('panel.ticketera.events.show', $event) }}" class="text-sm font-semibold text-primary">Ver</a>
                                    <a href="{{ route('panel.ticketera.events.edit', $event) }}" class="ml-3 text-sm font-semibold text-ink-soft">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
    @endif
@endsection
