@extends('admin.layouts.admin')

@section('title', 'Pagos · '.$business->name)

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <p class="text-sm text-ink-soft">Pagos de</p>
            <h1 class="text-3xl font-bold tracking-tight">{{ $business->name }}</h1>
        </div>
        <a href="{{ route('admin.billing.payments.index') }}" class="btn-ghost">← Volver a Pagos</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="space-y-4">
            @foreach ($paidModules as $module)
                @php($estado = $estados[$module->value])
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">{{ $module->label() }}</h2>
                        <span class="badge {{ $estado['status'] === 'al_dia' ? 'bg-[#e6f6ee] text-good' : ($estado['status'] === 'atrasado' ? 'bg-red-50 text-danger' : 'bg-app text-ink-soft') }}">
                            {{ $estado['status'] === 'al_dia' ? 'Al día' : ($estado['status'] === 'atrasado' ? 'Atrasado' : 'Inactivo') }}
                        </span>
                    </div>

                    <dl class="mt-3 space-y-1 text-sm">
                        <div class="flex justify-between"><dt class="text-ink-soft">Tarifa mensual</dt><dd class="font-medium">{{ $estado['price'] ? '$'.number_format($estado['price']) : 'Sin precio definido' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-soft">Último pago</dt><dd class="font-medium">{{ $estado['lastPayment'] ? $estado['lastPayment']->paid_on->format('d/m/Y').' · $'.number_format($estado['lastPayment']->amount) : '—' }}</dd></div>
                    </dl>

                    <form method="POST" action="{{ route('admin.billing.payments.store', $business) }}" class="mt-4 grid grid-cols-2 gap-3">
                        @csrf
                        <input type="hidden" name="module" value="{{ $module->value }}">
                        <div class="col-span-2">
                            <label class="label">Registrar pago (monto CLP)</label>
                            <input type="number" name="amount" class="input" min="100" value="{{ $estado['price'] }}" required>
                        </div>
                        <div>
                            <label class="label">Fecha del pago</label>
                            <input type="date" name="paid_on" class="input" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div>
                            <label class="label">Nota (opcional)</label>
                            <input type="text" name="notes" class="input" placeholder="Transferencia, efectivo…">
                        </div>
                        <button type="submit" class="btn-secondary col-span-2">Registrar pago</button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="card p-6 self-start">
            <h2 class="text-lg font-semibold">Historial de pagos</h2>

            @if ($pagos->isEmpty())
                <p class="mt-4 text-sm text-ink-faint">Aún no hay pagos registrados.</p>
            @else
                <ul class="mt-4 divide-y divide-line">
                    @foreach ($pagos as $pago)
                        <li class="py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold">{{ \App\Enums\Module::tryFrom($pago->module)?->label() }} · ${{ number_format($pago->amount) }}</span>
                                <span class="text-xs text-ink-faint">{{ $pago->paid_on->format('d/m/Y') }}</span>
                            </div>
                            @if ($pago->notes)
                                <p class="text-xs text-ink-soft mt-0.5">{{ $pago->notes }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
