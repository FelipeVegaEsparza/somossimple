@extends('layouts.panel')

@section('title', 'Mi plan')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-ink-soft">Mi negocio</p>
        <h1 class="text-3xl font-bold tracking-tight">Mi plan</h1>
        <p class="text-sm text-ink-soft mt-1">Resumen de tus servicios contratados, costo mensual y pagos.</p>
    </div>

    @php
        $statusLabel = fn ($status) => match ($status) {
            'atrasado' => 'Pago pendiente',
            'inactivo' => 'Inactivo',
            default => 'Al día',
        };
        $statusBadge = fn ($status) => match ($status) {
            'atrasado' => 'bg-red-50 text-danger',
            'inactivo' => 'bg-app text-ink-soft',
            default => 'bg-good/15 text-good',
        };
        $paidModules = $contracted->where('module')->filter(fn ($row) => ! $row['module']->isFree() && $row['price'] !== null);
        $nextCoverage = $paidModules->pluck('coverage_end')->filter()->sort()->first();
    @endphp

    @if ($overdue > 0)
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">
            <strong>Tienes {{ $overdue }} {{ $overdue === 1 ? 'módulo' : 'módulos' }} con pago pendiente.</strong>
            Actívalos de nuevo poniéndote al día para no perder el servicio.
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">Costo mensual</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight">${{ number_format($total, 0, ',', '.') }}</p>
            <p class="text-xs text-ink-soft mt-1">Servicios activos</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">Servicios contratados</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ $contracted->count() }}</p>
            <p class="text-xs text-ink-soft mt-1">Incluye el Perfil Digital gratis</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">Pagos pendientes</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight {{ $overdue > 0 ? 'text-danger' : '' }}">{{ $overdue }}</p>
            <p class="text-xs text-ink-soft mt-1">Módulos por regularizar</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">Cubierto hasta</p>
            <p class="mt-2 text-xl font-extrabold tracking-tight">{{ $nextCoverage?->format('d/m/Y') ?? '—' }}</p>
            <p class="text-xs text-ink-soft mt-1">Próximo vencimiento</p>
        </div>
    </div>

    <div class="card p-6 mt-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Servicios contratados</h2>
            <a href="{{ route('panel.modules') }}" class="text-sm font-semibold text-primary">Administrar módulos</a>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="table-header px-4 pb-2">Servicio</th>
                        <th class="table-header px-4 pb-2 text-right">Precio mensual</th>
                        <th class="table-header px-4 pb-2">Estado</th>
                        <th class="table-header px-4 pb-2">Cubierto hasta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($contracted as $row)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $row['module']->label() }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($row['module']->isFree())
                                    <span class="text-good font-semibold">Gratis</span>
                                @elseif ($row['price'] === null)
                                    <span class="text-ink-soft">A consultar</span>
                                @else
                                    ${{ number_format($row['price'], 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $statusBadge($row['status']) }}">{{ $statusLabel($row['status']) }}</span>
                            </td>
                            <td class="px-4 py-3 text-ink-soft">{{ $row['coverage_end']?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($withoutPrice > 0)
            <p class="mt-3 text-xs text-ink-faint">{{ $withoutPrice }} {{ $withoutPrice === 1 ? 'módulo está' : 'módulos están' }} activo sin precio definido; te contactaremos para coordinar.</p>
        @endif
    </div>

    <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-4 mt-4 items-start">
        <div class="card p-6">
            <h2 class="text-lg font-semibold">Pagos registrados</h2>
            @if ($payments->isEmpty())
                <p class="mt-3 text-sm text-ink-soft">Aún no hay pagos registrados.</p>
            @else
                <div class="overflow-x-auto mt-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="table-header px-4 pb-2">Fecha</th>
                                <th class="table-header px-4 pb-2">Servicio</th>
                                <th class="table-header px-4 pb-2 text-right">Monto</th>
                                <th class="table-header px-4 pb-2">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-ink-soft whitespace-nowrap">{{ $payment->paid_on->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">{{ \App\Enums\Module::tryFrom($payment->module)?->label() ?? $payment->module }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-ink-soft">{{ $payment->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Disponibles para activar</h2>
            <p class="text-sm text-ink-soft mt-0.5">Activa solo lo que tu negocio necesite.</p>

            @if ($available->isEmpty())
                <p class="mt-3 text-sm text-ink-soft">No hay más módulos disponibles por ahora.</p>
            @else
                <ul class="mt-3 divide-y divide-line">
                    @foreach ($available as $item)
                        <li class="py-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium">{{ $item['module']->label() }}</p>
                                <p class="text-xs text-ink-soft">${{ number_format($item['price'], 0, ',', '.') }} / mes</p>
                            </div>
                            <a href="{{ route('panel.modules') }}" class="text-sm font-semibold text-primary shrink-0">Solicitar</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <p class="mt-5 text-xs text-ink-faint">Los pagos se registran de forma manual por el equipo de SomosSimple. Si necesitas una boleta o detalle, escríbenos.</p>
@endsection
