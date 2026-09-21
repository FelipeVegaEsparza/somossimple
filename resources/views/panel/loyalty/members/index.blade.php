@extends('layouts.panel')

@section('title', 'Clientes de fidelización')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Fidelización</p>
            <h1 class="text-3xl font-bold tracking-tight">Clientes</h1>
            <p class="text-sm text-ink-soft mt-1">Cada cliente tiene un identificador único y su propia tarjeta.</p>
        </div>
        <a href="{{ route('panel.loyalty.members.create') }}" class="btn-primary">Nuevo cliente</a>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('panel.loyalty.members.index') }}" class="mb-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, teléfono o ID" class="input">
        <button type="submit" class="btn-secondary shrink-0">Buscar</button>
    </form>

    @if ($members->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no hay clientes</h2>
            <p class="text-sm text-ink-soft mt-1">Registra al primero o comparte tu página pública para que se registren solos.</p>
            <a href="{{ route('panel.loyalty.members.create') }}" class="btn-primary mt-5">Registrar cliente</a>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Identificador</th>
                            <th class="table-header px-4 py-3">Cliente</th>
                            <th class="table-header px-4 py-3">Contacto</th>
                            <th class="table-header px-4 py-3 text-right">{{ ucfirst($program->unit_name) }}</th>
                            <th class="table-header px-4 py-3">Última actividad</th>
                            <th class="table-header px-4 py-3">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($members as $member)
                            <tr class="hover:bg-app/60">
                                <td class="px-4 py-3 font-mono text-xs text-ink-soft">{{ $member->code }}</td>
                                <td class="px-4 py-3 font-medium">{{ $member->name }}</td>
                                <td class="px-4 py-3 text-ink-soft">
                                    {{ $member->phone ?: '—' }}
                                    @if ($member->email)<span class="block text-xs">{{ $member->email }}</span>@endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">{{ number_format($member->balanceFor($program->type()), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ $member->last_activity_at?->diffForHumans() ?? 'Sin actividad' }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $member->isActive() ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $member->isActive() ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('panel.loyalty.members.show', $member) }}" class="text-sm font-semibold text-primary">Ver ficha</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $members->links() }}</div>
    @endif
@endsection
