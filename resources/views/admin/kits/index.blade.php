@extends('admin.layouts.admin')

@section('title', 'Kits')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Kits</h1>
            <p class="text-sm text-ink-soft mt-1">Stock de tótems (QR + NFC) para la venta. El comprador activa su serial desde su panel.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-3 gap-4 mb-4 max-w-lg">
        <div class="card p-4">
            <p class="text-2xl font-bold">{{ number_format($counts['available']) }}</p>
            <p class="text-xs text-ink-soft">Disponibles</p>
        </div>
        <div class="card p-4">
            <p class="text-2xl font-bold">{{ number_format($counts['sold']) }}</p>
            <p class="text-xs text-ink-soft">Vendidos</p>
        </div>
        <div class="card p-4">
            <p class="text-2xl font-bold">{{ number_format($counts['activated']) }}</p>
            <p class="text-xs text-ink-soft">Activados</p>
        </div>
    </div>

    <div class="card p-6 mb-4 max-w-xl">
        <h2 class="text-lg font-semibold">Registrar kit en stock</h2>
        <form method="POST" action="{{ route('admin.kits.store') }}" class="mt-4 grid grid-cols-2 gap-3">
            @csrf
            <div>
                <label class="label">Tipo</label>
                <select name="type" class="input" required>
                    <option value="qr_nfc">QR + NFC (tótem)</option>
                    <option value="qr">QR</option>
                    <option value="nfc">NFC</option>
                </select>
            </div>
            <div>
                <label class="label">Serial (opcional)</label>
                <input type="text" name="serial" class="input" placeholder="Vacío = se genera automático">
            </div>
            <button type="submit" class="btn-secondary col-span-2">Registrar kit disponible</button>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.kits.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="label">Buscar</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Serial o negocio" class="input">
        </div>
        <div>
            <label class="label">Estado</label>
            <select name="status" class="input">
                <option value="">Todos</option>
                <option value="available" @selected(request('status') === 'available')>Disponible</option>
                <option value="sold" @selected(request('status') === 'sold')>Vendido</option>
                <option value="activated" @selected(request('status') === 'activated')>Activado</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary">Filtrar</button>
        @if (request('q') || request('status') || request('negocio'))
            <a href="{{ route('admin.kits.index') }}" class="btn-ghost">Limpiar</a>
        @endif
    </form>

    @if ($kits->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin kits</h2>
            <p class="text-sm text-ink-soft mt-1">Registra kits disponibles para poder venderlos.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-line bg-app">
                        <th class="table-header px-5 py-3">Serial</th>
                        <th class="table-header px-5 py-3">Tipo</th>
                        <th class="table-header px-5 py-3">Estado</th>
                        <th class="table-header px-5 py-3">Negocio</th>
                        <th class="table-header px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($kits as $kit)
                        <tr>
                            <td class="px-5 py-3 font-mono text-sm font-semibold">{{ $kit->serial }}</td>
                            <td class="px-5 py-3 text-sm text-ink-soft">{{ $kit->typeLabel() }}</td>
                            <td class="px-5 py-3">
                                <span class="badge {{ $kit->status === 'activated' ? 'bg-primary-tint text-primary' : ($kit->status === 'sold' ? 'bg-[#fff6e0] text-warn' : 'bg-[#e6f6ee] text-good') }}">
                                    {{ $kit->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-ink-soft">
                                @if ($kit->business)
                                    <a href="{{ route('admin.business.show', $kit->business) }}" class="text-primary font-semibold">{{ $kit->business->name }}</a>
                                @elseif ($kit->status === 'sold')
                                    <span class="text-ink-faint">Esperando activación</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.kits.qr', ['code' => $kit]) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-ink-soft hover:text-ink">Ver QR</a>
                                    <a href="{{ route('admin.kits.qr', ['code' => $kit, 'descargar' => 1]) }}" class="text-xs font-semibold text-ink-soft hover:text-ink">Descargar</a>
                                    @if ($kit->status === 'available')
                                        <form method="POST" action="{{ route('admin.kits.sell', $kit) }}" onsubmit="return confirm('¿Vender el kit {{ $kit->serial }}?')">
                                            @csrf
                                            <button type="submit" class="btn-secondary">Vender</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center gap-2">
            @if ($kits->previousPageUrl())
                <a href="{{ $kits->previousPageUrl() }}" class="btn-secondary px-3">← Anterior</a>
            @endif
            @if ($kits->nextPageUrl())
                <a href="{{ $kits->nextPageUrl() }}" class="btn-secondary px-3">Siguiente →</a>
            @endif
        </div>
    @endif
@endsection
