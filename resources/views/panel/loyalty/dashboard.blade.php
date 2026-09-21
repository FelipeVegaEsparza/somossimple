@extends('layouts.panel')

@section('title', 'Fidelización')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Módulo</p>
            <h1 class="text-3xl font-bold tracking-tight">Fidelización</h1>
            <p class="text-sm text-ink-soft mt-1">
                {{ $program->name }}
                <span class="badge ml-1 {{ $program->is_active ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $program->is_active ? 'Activo' : 'Inactivo' }}</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('panel.loyalty.quick') }}" class="btn-primary">Registrar actividad</a>
            <a href="{{ route('loyalty.public', $business->slug) }}" target="_blank" rel="noopener" class="btn-secondary">Ver página pública</a>
        </div>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ([
            ['Clientes', $stats['members']],
            ['Clientes activos', $stats['active']],
            ['Puntos emitidos', $stats['points_issued']],
            ['Puntos canjeados', $stats['points_redeemed']],
            ['Recompensas activas', $stats['rewards']],
            ['Canjes realizados', $stats['redemptions']],
        ] as $card)
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">{{ $card[0] }}</p>
                <p class="mt-2 text-2xl font-extrabold tracking-tight">{{ number_format($card[1], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="card p-6 mt-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Actividad reciente</h2>
            <a href="{{ route('panel.loyalty.activities.index') }}" class="text-sm font-semibold text-primary">Ver todo</a>
        </div>

        @if ($recent->isEmpty())
            <p class="mt-3 text-sm text-ink-soft">Aún no hay movimientos. Registra el primero desde «Registrar».</p>
        @else
            <ul class="mt-3 divide-y divide-line">
                @foreach ($recent as $activity)
                    <li class="py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium truncate">
                                {{ $activity->member?->name }}
                                <span class="text-ink-faint font-normal">· {{ $activity->member?->code }}</span>
                            </p>
                            <p class="text-xs text-ink-soft">
                                {{ $activity->type->label() }}@if ($activity->reason) · {{ $activity->reason }}@endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-semibold {{ $activity->units > 0 ? 'text-good' : ($activity->units < 0 ? 'text-danger' : 'text-ink-soft') }}">{{ $activity->unitsDisplay() }}</p>
                            <p class="text-xs text-ink-faint">{{ $activity->created_at->format('d/m H:i') }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
