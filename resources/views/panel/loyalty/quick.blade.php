@extends('layouts.panel')

@section('title', 'Registrar actividad')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Fidelización</p>
        <h1 class="text-3xl font-bold tracking-tight">Registrar actividad</h1>
        <p class="text-sm text-ink-soft mt-1">Busca al cliente y registra puntos, visitas, sellos o un canje en segundos.</p>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('panel.loyalty.quick') }}" class="flex gap-2 max-w-xl">
        <input type="text" name="q" value="{{ $q }}" autofocus class="input h-12 text-base" placeholder="Escanea o escribe el código, nombre o teléfono del cliente">
        <button type="submit" class="btn-primary h-12 px-6 shrink-0">Buscar</button>
    </form>
    <p class="mt-2 text-xs text-ink-faint">Puedes escanear el QR del cliente con la cámara del teléfono: se abrirá su tarjeta.</p>

    @if ($q !== '' && $members->isEmpty())
        <div class="card p-6 mt-4">
            <p class="text-sm text-ink-soft">No encontramos clientes con «{{ $q }}».</p>
        </div>
    @endif

    @if ($members->isNotEmpty())
        <div class="mt-4 space-y-3">
            @foreach ($members as $member)
                <div class="card p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('panel.loyalty.members.show', $member) }}" class="font-semibold text-primary">{{ $member->name }}</a>
                            <p class="text-xs text-ink-soft font-mono">{{ $member->code }} · {{ number_format($member->balanceFor($program->type()), 0, ',', '.') }} {{ $program->unit_name }}</p>
                        </div>
                        <span class="badge {{ $member->isActive() ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $member->isActive() ? 'Activo' : 'Inactivo' }}</span>
                    </div>

                    @if ($member->isActive())
                        <div class="mt-3 grid sm:grid-cols-2 gap-2">
                            <form method="POST" action="{{ route('panel.loyalty.quick.store') }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="member" value="{{ $member->code }}">
                                <input type="hidden" name="action" value="points">
                                <input type="number" name="amount" min="1" value="{{ $program->earn_units ?: 1 }}" class="input w-28" aria-label="Cantidad">
                                <button type="submit" class="btn-secondary flex-1">+ {{ $program->unit_name }}</button>
                            </form>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('panel.loyalty.quick.store') }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="member" value="{{ $member->code }}">
                                    <input type="hidden" name="action" value="visit">
                                    <button type="submit" class="btn-secondary w-full">+ Visita</button>
                                </form>
                                <form method="POST" action="{{ route('panel.loyalty.quick.store') }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="member" value="{{ $member->code }}">
                                    <input type="hidden" name="action" value="stamp">
                                    <button type="submit" class="btn-secondary w-full">+ Sello</button>
                                </form>
                            </div>

                            @php($available = $member->availableRewards())
                            @if ($available->isNotEmpty())
                                <form method="POST" action="{{ route('panel.loyalty.quick.store') }}" class="sm:col-span-2 flex gap-2" onsubmit="return confirm('¿Confirmar el canje de la recompensa seleccionada?')">
                                    @csrf
                                    <input type="hidden" name="member" value="{{ $member->code }}">
                                    <input type="hidden" name="action" value="redeem">
                                    <select name="reward_id" class="input">
                                        @foreach ($available as $reward)
                                            <option value="{{ $reward->id }}">Canjear: {{ $reward->name }} ({{ $reward->requirementDisplay() }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-primary shrink-0">Canjear</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
