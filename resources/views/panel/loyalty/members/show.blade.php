@extends('layouts.panel')

@section('title', $member->name)

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Fidelización · Clientes</p>
            <h1 class="text-3xl font-bold tracking-tight">{{ $member->name }}</h1>
            <p class="mt-1 flex items-center gap-2 text-sm text-ink-soft">
                <span class="font-mono">{{ $member->code }}</span>
                <span class="badge {{ $member->isActive() ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $member->isActive() ? 'Activo' : 'Inactivo' }}</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('panel.loyalty.members.index') }}" class="btn-ghost">← Volver</a>
            <form method="POST" action="{{ route('panel.loyalty.members.status', $member) }}">
                @csrf
                <button type="submit" class="btn-secondary">{{ $member->isActive() ? 'Desactivar' : 'Activar' }}</button>
            </form>
            <form method="POST" action="{{ route('panel.loyalty.members.destroy', $member) }}" onsubmit="return confirm('¿Eliminar a {{ $member->name }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-secondary border-danger/40 text-danger">Eliminar</button>
            </form>
        </div>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-4 items-start">
        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Tarjeta digital</h2>
                <div class="mt-4 flex justify-center">
                    @include('loyalty._card', [
                        'business' => $business,
                        'program' => $program,
                        'member' => $member,
                        'qrUrl' => route('panel.loyalty.members.qr', $member),
                    ])
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('panel.loyalty.members.qr', $member) }}" target="_blank" rel="noopener" class="btn-secondary">Mostrar QR</a>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Registrar actividad</h2>
                <div class="mt-4 space-y-3">
                    <form method="POST" action="{{ route('panel.loyalty.quick.store') }}" class="flex flex-wrap gap-2">
                        @csrf
                        <input type="hidden" name="member" value="{{ $member->code }}">
                        <input type="hidden" name="action" value="points">
                        <input type="number" name="amount" min="1" value="{{ $program->earn_units ?: 1 }}" class="input w-32" aria-label="Cantidad de {{ $program->unit_name }}">
                        <button type="submit" class="btn-secondary">Sumar {{ $program->unit_name }}</button>
                    </form>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('panel.loyalty.quick.store') }}">
                            @csrf
                            <input type="hidden" name="member" value="{{ $member->code }}">
                            <input type="hidden" name="action" value="visit">
                            <button type="submit" class="btn-secondary">+ Visita</button>
                        </form>
                        <form method="POST" action="{{ route('panel.loyalty.quick.store') }}">
                            @csrf
                            <input type="hidden" name="member" value="{{ $member->code }}">
                            <input type="hidden" name="action" value="stamp">
                            <button type="submit" class="btn-secondary">+ Sello</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Recompensas</h2>
                <p class="text-sm text-ink-soft mt-0.5">Disponibles para canjear ahora.</p>

                @forelse ($availableRewards as $reward)
                    <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-line p-3">
                        <div>
                            <p class="font-semibold">{{ $reward->name }}</p>
                            <p class="text-xs text-ink-soft">Requiere {{ $reward->requirementDisplay() }}</p>
                        </div>
                        <form method="POST" action="{{ route('panel.loyalty.members.reward', [$member, $reward]) }}" onsubmit="return confirm('¿Confirmar canje de {{ $reward->name }}?')">
                            @csrf
                            <button type="submit" class="btn-primary">Canjear</button>
                        </form>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-ink-soft">Todavía no alcanza ninguna recompensa.</p>
                @endforelse

                @if ($allRewards->isNotEmpty())
                    <div class="mt-4 border-t border-line pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">Todas las recompensas</p>
                        <ul class="mt-2 divide-y divide-line text-sm">
                            @foreach ($allRewards as $reward)
                                <li class="py-2 flex items-center justify-between gap-3">
                                    <span>{{ $reward->name }}</span>
                                    <span class="text-ink-soft">{{ $reward->requirementDisplay() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Tarjetas en Wallet</h2>
                <p class="text-sm text-ink-soft mt-0.5">El cliente puede guardar su tarjeta en Apple Wallet o Google Wallet.</p>

                <div class="mt-4 space-y-3">
                    @foreach ($walletPlatforms as $platform)
                        @php($card = $walletCards[$platform->value] ?? null)
                        @php($configured = $walletConfigured[$platform->value] ?? false)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-line p-3">
                            <div class="min-w-0">
                                <p class="font-semibold">{{ $platform->label() }}</p>
                                <p class="text-xs text-ink-soft">
                                    Estado: {{ $card?->status->label() ?? 'No agregada' }}
                                    @if ($card?->error_message) · {{ $card->error_message }} @endif
                                </p>
                            </div>
                            @if ($configured)
                                <form method="POST" action="{{ route('panel.loyalty.members.wallet', [$member, $platform->value]) }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="btn-secondary">Agregar a {{ $platform->shortLabel() }} Wallet</button>
                                </form>
                            @else
                                <span class="shrink-0 text-xs font-semibold text-ink-soft">{{ $platform->label() }} aún no está configurado.</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Datos</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-soft">Teléfono</dt><dd>{{ $member->phone ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Correo</dt><dd>{{ $member->email ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Registrado</dt><dd>{{ $member->created_at->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Última actividad</dt><dd>{{ $member->last_activity_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                </dl>

                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-lg bg-app py-2"><p class="text-lg font-bold">{{ number_format($member->points, 0, ',', '.') }}</p><p class="text-[11px] text-ink-soft">puntos</p></div>
                    <div class="rounded-lg bg-app py-2"><p class="text-lg font-bold">{{ number_format($member->visits, 0, ',', '.') }}</p><p class="text-[11px] text-ink-soft">visitas</p></div>
                    <div class="rounded-lg bg-app py-2"><p class="text-lg font-bold">{{ number_format($member->stamps, 0, ',', '.') }}</p><p class="text-[11px] text-ink-soft">sellos</p></div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Historial</h2>
                <ul class="mt-3 divide-y divide-line">
                    @forelse ($activities as $activity)
                        <li class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium">{{ $activity->type->label() }}</p>
                                <p class="text-xs text-ink-soft truncate">{{ $activity->reason ?: '—' }} · {{ $activity->user?->name ?? 'Sistema' }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-semibold {{ $activity->units > 0 ? 'text-good' : ($activity->units < 0 ? 'text-danger' : 'text-ink-soft') }}">{{ $activity->unitsDisplay() }}</p>
                                <p class="text-xs text-ink-faint">{{ $activity->created_at->format('d/m H:i') }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-ink-soft">Sin movimientos todavía.</li>
                    @endforelse
                </ul>
                <div class="mt-3">{{ $activities->links() }}</div>
            </div>
        </div>
    </div>
@endsection
