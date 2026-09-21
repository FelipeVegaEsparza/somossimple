@extends('admin.layouts.admin')

@section('title', 'Pagos')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight">Pagos</h1>
        <p class="text-sm text-ink-soft mt-1">Estado de pago por negocio y módulo. Registra aquí los pagos recurrentes.</p>
    </div>

    @if (! $preciosDefinidos)
        <div class="mb-4 rounded-xl border border-line bg-app px-4 py-3 text-sm text-ink-soft">
            Aún no defines precios: mientras un módulo no tenga precio, la regla de impago no se aplica.
            <a href="{{ route('admin.billing.prices.index') }}" class="font-semibold text-primary">Definir precios</a>.
        </div>
    @endif

    <form method="GET" action="{{ route('admin.billing.payments.index') }}" class="mb-4 flex gap-3 max-w-xl">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por negocio o correo" class="input">
        <button type="submit" class="btn-secondary shrink-0">Buscar</button>
        @if (request('q'))
            <a href="{{ route('admin.billing.payments.index') }}" class="btn-ghost">Limpiar</a>
        @endif
    </form>

    @if ($negocios->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin resultados</h2>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-line bg-app">
                        <th class="table-header px-5 py-3">Negocio</th>
                        @foreach ($paidModules as $module)
                            <th class="table-header px-3 py-3 text-center">{{ $module->label() }}</th>
                        @endforeach
                        <th class="table-header px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($negocios as $business)
                        @php($moduleAccess = $business->moduleAccess->keyBy('module'))
                        <tr>
                            <td class="px-5 py-3">
                                <p class="text-sm font-semibold">{{ $business->name }}</p>
                                <p class="text-xs text-ink-faint">{{ $business->account->email }}</p>
                            </td>
                            @foreach ($paidModules as $module)
                                <td class="px-3 py-3 text-center">
                                    @if (! $moduleAccess->get($module->value)?->active)
                                        <span class="badge bg-app text-ink-soft">Inactivo</span>
                                    @else
                                        @php($status = $billing->statusFor($business, $module))
                                        <span class="badge {{ $status === 'al_dia' ? 'bg-[#e6f6ee] text-good' : ($status === 'atrasado' ? 'bg-red-50 text-danger' : 'bg-app text-ink-soft') }}">
                                            {{ $status === 'al_dia' ? 'Al día' : ($status === 'atrasado' ? 'Atrasado' : 'Sin precio') }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.billing.payments.show', $business) }}" class="text-sm font-semibold text-primary">Pagos</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center gap-2">
            @if ($negocios->previousPageUrl())
                <a href="{{ $negocios->previousPageUrl() }}" class="btn-secondary px-3">← Anterior</a>
            @endif
            @if ($negocios->nextPageUrl())
                <a href="{{ $negocios->nextPageUrl() }}" class="btn-secondary px-3">Siguiente →</a>
            @endif
        </div>
    @endif
@endsection
