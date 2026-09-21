@extends('admin.layouts.admin')

@section('title', 'Solicitudes de kits')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Solicitudes de kits</h1>
            <p class="text-sm text-ink-soft mt-1">Pedidos de la landing. Contacta y concreta la venta; la activación la hace el cliente en su panel.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <div class="card p-4 mb-4 max-w-xs">
        <p class="text-2xl font-bold">{{ number_format($nuevas) }}</p>
        <p class="text-xs text-ink-soft">Solicitudes nuevas</p>
    </div>

    @if ($requests->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin solicitudes</h2>
            <p class="text-sm text-ink-soft mt-1">Los pedidos de la landing aparecerán aquí.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-line bg-app">
                        <th class="table-header px-5 py-3">Contacto</th>
                        <th class="table-header px-5 py-3">Negocio</th>
                        <th class="table-header px-5 py-3">WhatsApp / Email</th>
                        <th class="table-header px-5 py-3">Cantidad</th>
                        <th class="table-header px-5 py-3">Estado</th>
                        <th class="table-header px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($requests as $solicitud)
                        <tr>
                            <td class="px-5 py-3 text-sm font-semibold">{{ $solicitud->name }}
                                <div class="text-xs text-ink-faint font-normal">{{ $solicitud->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-5 py-3 text-sm text-ink-soft">{{ $solicitud->business_name ?: '—' }}</td>
                            <td class="px-5 py-3 text-sm text-ink-soft">{{ $solicitud->whatsapp ?: '—' }}<br>{{ $solicitud->email ?: '' }}</td>
                            <td class="px-5 py-3 text-sm">{{ $solicitud->quantity }}</td>
                            <td class="px-5 py-3">
                                <span class="badge {{ $solicitud->status === 'nueva' ? 'bg-[#fff6e0] text-warn' : ($solicitud->status === 'contactada' ? 'bg-primary-tint text-primary' : 'bg-app text-ink-soft') }}">
                                    {{ ucfirst($solicitud->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if ($solicitud->status === 'nueva')
                                        <form method="POST" action="{{ route('admin.kitrequests.contact', $solicitud) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-primary">Marcar contactada</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.kitrequests.destroy', $solicitud) }}" onsubmit="return confirm('¿Eliminar esta solicitud?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center gap-2">
            @if ($requests->previousPageUrl())
                <a href="{{ $requests->previousPageUrl() }}" class="btn-secondary px-3">← Anterior</a>
            @endif
            @if ($requests->nextPageUrl())
                <a href="{{ $requests->nextPageUrl() }}" class="btn-secondary px-3">Siguiente →</a>
            @endif
        </div>
    @endif
@endsection
