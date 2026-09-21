@extends('admin.layouts.admin')

@section('title', 'Activaciones')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight">Solicitudes de módulos</h1>
        <p class="text-sm text-ink-soft mt-1">Los clientes piden activar o desactivar sus módulos de pago desde su panel. Aprobadas, se aplican al momento.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    @if ($pending->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Sin solicitudes pendientes</h2>
            <p class="text-sm text-ink-soft mt-1">Las solicitudes de activación o desactivación de tus clientes aparecerán aquí.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <ul class="divide-y divide-line">
                @foreach ($pending as $solicitud)
                    @php($modulo = \App\Enums\Module::from($solicitud->module))
                    <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold">
                                {{ $solicitud->business->name }}
                                <span class="text-ink-faint font-normal">· {{ $solicitud->business->account->email }}</span>
                            </p>
                            <p class="text-sm text-ink-soft mt-0.5">
                                {{ $solicitud->action === 'activate' ? 'Solicita ACTIVAR' : 'Solicita DESACTIVAR' }}
                                el módulo <strong>{{ $modulo->label() }}</strong>
                                <span class="text-ink-faint">· {{ $solicitud->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form method="POST" action="{{ route('admin.modulerequests.approve', $solicitud) }}" onsubmit="return confirm('¿Aplicar esta solicitud?')">
                                @csrf
                                <button type="submit" class="btn-primary">Aprobar y aplicar</button>
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
@endsection
