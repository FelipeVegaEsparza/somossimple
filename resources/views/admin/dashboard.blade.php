@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-sm text-ink-soft mt-1">Resumen y tareas pendientes de la plataforma.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.business.index') }}" class="card p-5 flex items-center gap-3 hover:border-primary/40 transition-colors">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v2.25M18.75 3v2.25M6 5.25h12l.75 15.75h-13.5L6 5.25ZM9 9h2.25m-2.25 3.75h4.5"/></svg>
            </span>
            <div>
                <p class="text-3xl font-bold tracking-tight">{{ number_format($total) }}</p>
                <p class="text-xs text-ink-soft mt-1">Negocios</p>
            </div>
        </a>
        <div class="card p-5 flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </span>
            <div>
                <p class="text-3xl font-bold tracking-tight">{{ number_format($modulosActivos) }}</p>
                <p class="text-xs text-ink-soft mt-1">Módulos de pago activos</p>
            </div>
        </div>
        <a href="{{ route('admin.kits.index') }}" class="card p-5 flex items-center gap-3 hover:border-primary/40 transition-colors">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
            </span>
            <div>
                <p class="text-3xl font-bold tracking-tight">{{ $kits['sold'] + $kits['activated'] }}</p>
                <p class="text-xs text-ink-soft mt-1">Kits vendidos</p>
            </div>
        </a>
        <a href="{{ route('admin.kitrequests.index') }}" class="card p-5 flex items-center gap-3 hover:border-primary/40 transition-colors">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
            </span>
            <div>
                <p class="text-3xl font-bold tracking-tight text-warn">{{ number_format($solicitudesKits) }}</p>
                <p class="text-xs text-ink-soft mt-1">Solicitudes de kits</p>
            </div>
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-4 mt-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Actividad de la plataforma</h2>
                        <p class="text-xs text-ink-faint mt-0.5">Últimos 7 días</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-ink-soft">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background: var(--color-primary)"></span> Visitas</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-ink-faint"></span> Escaneos</span>
                    </div>
                </div>
                <div class="mt-5 flex items-end justify-between gap-3 h-24">
                    @foreach ($platformWeekly as $day)
                        @php($hv = max(4, round($day['visits'] / $platformMax * 80)))
                        @php($hs = max(4, round($day['scans'] / $platformMax * 80)))
                        <div class="flex-1 flex flex-col items-center justify-end gap-1.5">
                            <div class="w-full flex items-end justify-center gap-1">
                                <div class="w-2 rounded-t-sm" style="height: {{ $hv }}px; background: var(--color-primary)"></div>
                                <div class="w-2 rounded-t-sm bg-ink-faint" style="height: {{ $hs }}px"></div>
                            </div>
                            <span class="text-[11px] text-ink-faint">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($activaciones->isNotEmpty())
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Activaciones pendientes</h2>
                        <a href="{{ route('admin.modulerequests.index') }}" class="text-sm font-semibold text-primary">Ver todas</a>
                    </div>
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($activaciones->take(4) as $solicitud)
                            <li class="py-3 flex flex-wrap items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold truncate">{{ $solicitud->business->name }}</p>
                                    <p class="text-sm text-ink-soft">
                                        <span class="badge {{ $solicitud->action === 'activate' ? 'bg-primary-tint text-primary' : 'bg-red-50 text-danger' }}">{{ $solicitud->action === 'activate' ? 'Activar' : 'Desactivar' }}</span>
                                        <span class="ml-1">{{ \App\Enums\Module::from($solicitud->module)->label() }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('admin.modulerequests.approve', $solicitud) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary">Aprobar</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.modulerequests.decline', $solicitud) }}">
                                        @csrf
                                        <button type="submit" class="btn-secondary">Rechazar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($atrasados->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-semibold">Cobrar (módulos atrasados)</h2>
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($atrasados as $item)
                            <li class="py-3 flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold">{{ $item['business']->name }}</p>
                                    <p class="text-sm text-ink-soft">{{ $item['module']->label() }} sin pago al día</p>
                                </div>
                                <a href="{{ route('admin.billing.payments.show', $item['business']) }}" class="text-sm font-semibold text-primary shrink-0">Registrar pago</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Negocios recientes</h2>
                    <a href="{{ route('admin.business.index') }}" class="text-sm font-semibold text-primary">Ver todos</a>
                </div>
                @if ($recientes->isEmpty())
                    <p class="mt-4 text-sm text-ink-faint">Aún no hay negocios registrados.</p>
                @else
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($recientes as $business)
                            <li class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold truncate">{{ $business->name }}</p>
                                    <p class="text-xs text-ink-faint truncate">{{ $business->account->email }}</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="badge {{ $business->is_paused ? 'bg-red-50 text-danger' : 'bg-[#e6f6ee] text-good' }}">{{ $business->is_paused ? 'Pausado' : 'Activo' }}</span>
                                    <a href="{{ route('admin.business.show', $business) }}" class="text-sm font-semibold text-primary">Ficha</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Kits</h2>
                @php($kTotal = max(1, $kits['available'] + $kits['sold'] + $kits['activated']))
                @php($pA = round($kits['activated'] / $kTotal * 100))
                @php($pS = round($kits['sold'] / $kTotal * 100))
                @php($pAv = max(0, 100 - $pA - $pS))
                <div class="mt-4 flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full shrink-0"
                         style="background: conic-gradient(var(--color-primary) 0 {{ $pA }}%, var(--color-warn) {{ $pA }}% {{ $pA + $pS }}%, var(--color-line) {{ $pA + $pS }}% 100%);">
                        <div class="w-full h-full grid place-items-center"><span class="w-14 h-14 rounded-full bg-surface"></span></div>
                    </div>
                    <ul class="text-xs text-ink-soft space-y-1">
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-sm" style="background: var(--color-primary)"></span> Activados · {{ $kits['activated'] }}</li>
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-sm" style="background: var(--color-warn)"></span> Vendidos · {{ $kits['sold'] }}</li>
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-sm" style="background: var(--color-line)"></span> Disponibles · {{ $kits['available'] }}</li>
                    </ul>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <a href="{{ route('admin.kits.index', ['status' => 'available']) }}" class="rounded-xl bg-app p-3 text-center hover:bg-primary-tint">
                        <p class="text-xl font-bold">{{ number_format($kits['available']) }}</p>
                        <p class="text-xs text-ink-soft">Disponibles</p>
                    </a>
                    <a href="{{ route('admin.kits.index', ['status' => 'sold']) }}" class="rounded-xl bg-app p-3 text-center hover:bg-primary-tint">
                        <p class="text-xl font-bold">{{ number_format($kits['sold']) }}</p>
                        <p class="text-xs text-ink-soft">Vendidos</p>
                    </a>
                    <a href="{{ route('admin.kits.index', ['status' => 'activated']) }}" class="rounded-xl bg-app p-3 text-center hover:bg-primary-tint">
                        <p class="text-xl font-bold">{{ number_format($kits['activated']) }}</p>
                        <p class="text-xs text-ink-soft">Activados</p>
                    </a>
                </div>
                <a href="{{ route('admin.kits.index') }}" class="btn-secondary w-full mt-4">Gestionar kits</a>
            </div>

            @if ($topEscaneos->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-semibold">Top escaneos de kits</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach ($topEscaneos as $fila)
                            <li class="flex items-center justify-between gap-3">
                                <span class="text-sm text-ink-soft truncate">{{ $fila->business?->name }}</span>
                                <span class="font-bold">{{ number_format($fila->total) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection
