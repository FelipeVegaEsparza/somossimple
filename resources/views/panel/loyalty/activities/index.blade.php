@extends('layouts.panel')

@section('title', 'Actividad de fidelización')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Fidelización</p>
        <h1 class="text-3xl font-bold tracking-tight">Historial de actividad</h1>
        <p class="text-sm text-ink-soft mt-1">Cada suma, canje o ajuste queda registrado con fecha, usuario y motivo.</p>
    </div>

    @include('panel.loyalty._nav')

    <form method="GET" action="{{ route('panel.loyalty.activities.index') }}" class="mb-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por cliente o ID" class="input">
        <button type="submit" class="btn-secondary shrink-0">Buscar</button>
    </form>

    @if ($activities->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin movimientos</h2>
            <p class="text-sm text-ink-soft mt-1">Aquí verás el historial completo del programa.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Fecha</th>
                            <th class="table-header px-4 py-3">Cliente</th>
                            <th class="table-header px-4 py-3">Operación</th>
                            <th class="table-header px-4 py-3 text-right">Cantidad</th>
                            <th class="table-header px-4 py-3">Motivo</th>
                            <th class="table-header px-4 py-3">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($activities as $activity)
                            <tr class="hover:bg-app/60">
                                <td class="px-4 py-3 whitespace-nowrap text-ink-soft">{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    @if ($activity->member)
                                        <a href="{{ route('panel.loyalty.members.show', $activity->member) }}" class="font-medium text-primary">{{ $activity->member->name }}</a>
                                        <span class="block font-mono text-xs text-ink-faint">{{ $activity->member->code }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $activity->type->label() }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $activity->units > 0 ? 'text-good' : ($activity->units < 0 ? 'text-danger' : 'text-ink-soft') }}">{{ $activity->unitsDisplay() }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ $activity->reason ?: '—' }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ $activity->user?->name ?? 'Sistema' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $activities->links() }}</div>
    @endif
@endsection
