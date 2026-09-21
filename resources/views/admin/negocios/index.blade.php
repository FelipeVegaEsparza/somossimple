@extends('admin.layouts.admin')

@section('title', 'Negocios')

@section('content')
    <div class="flex items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Negocios</h1>
            <p class="text-sm text-ink-soft mt-1">Cuentas y negocios del sistema. Solo el Perfil Digital es gratis; el resto se activa por separado.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4 max-w-md">
        <div class="card p-4">
            <p class="text-2xl font-bold">{{ number_format($total) }}</p>
            <p class="text-xs text-ink-soft">Negocios</p>
        </div>
        <div class="card p-4">
            <p class="text-2xl font-bold">{{ number_format($pausados) }}</p>
            <p class="text-xs text-ink-soft">Pausados</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.business.index') }}" class="mb-4 flex gap-3 max-w-xl">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por negocio, nombre o correo" class="input">
        <button type="submit" class="btn-secondary shrink-0">Buscar</button>
        @if (request('q'))
            <a href="{{ route('admin.business.index') }}" class="btn-ghost">Limpiar</a>
        @endif
    </form>

    @if ($negocios->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin resultados</h2>
            <p class="text-sm text-ink-soft mt-1">Prueba con otra búsqueda.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-line bg-app">
                        <th class="table-header px-5 py-3">Negocio</th>
                        <th class="table-header px-5 py-3">Cuenta</th>
                        <th class="table-header px-5 py-3">Estado</th>
                        <th class="table-header px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($negocios as $business)
                        <tr>
                            <td class="px-5 py-3">
                                <p class="text-sm font-semibold">{{ $business->name }}</p>
                                <p class="text-xs text-ink-faint font-mono">/{{ $business->slug }}</p>
                            </td>
                            <td class="px-5 py-3 text-sm text-ink-soft">{{ $business->account->name }}<br>{{ $business->account->email }}</td>
                            <td class="px-5 py-3">
                                @if ($business->is_paused)
                                    <span class="badge bg-red-50 text-danger">Pausado</span>
                                @else
                                    <span class="badge bg-[#e6f6ee] text-good">Activo</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.business.show', $business) }}" class="text-sm font-semibold text-primary">Abrir ficha</a>
                                    <form method="POST" action="{{ route('admin.business.pause', $business) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-ink-soft hover:text-ink">{{ $business->is_paused ? 'Reactivar' : 'Suspender' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.business.destroy', $business) }}" onsubmit="return confirm('¿Eliminar la cuenta de {{ $business->name }} y todos sus datos? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-danger hover:underline">Eliminar</button>
                                    </form>
                                </div>
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
