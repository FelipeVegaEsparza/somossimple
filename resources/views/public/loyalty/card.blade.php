<x-module-page :business="$business" title="Mi tarjeta" :icon="\App\Enums\Module::Loyalty->icon()" :tagline="$program->name">
    @if (session('loyalty_ok'))
        <div class="mb-4 rounded-xl bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('loyalty_ok') }}</div>
    @endif

    <div class="flex justify-center">
        @include('loyalty._card', [
            'business' => $business,
            'program' => $program,
            'member' => $member,
            'qrUrl' => route('loyalty.qr', [$business->slug, $member->token]),
        ])
    </div>

    <p class="mt-3 text-center text-xs text-ink-faint">Muestra este código QR en el negocio para registrar tu actividad.</p>

    <div class="mt-5 grid grid-cols-3 gap-2 text-center">
        <div class="rounded-xl border border-line py-3"><p class="text-xl font-extrabold">{{ number_format($member->points, 0, ',', '.') }}</p><p class="text-[11px] text-ink-soft">puntos</p></div>
        <div class="rounded-xl border border-line py-3"><p class="text-xl font-extrabold">{{ number_format($member->visits, 0, ',', '.') }}</p><p class="text-[11px] text-ink-soft">visitas</p></div>
        <div class="rounded-xl border border-line py-3"><p class="text-xl font-extrabold">{{ number_format($member->stamps, 0, ',', '.') }}</p><p class="text-[11px] text-ink-soft">sellos</p></div>
    </div>

    @if ($availableRewards->isNotEmpty())
        <h2 class="mt-6 text-sm font-bold uppercase tracking-wider text-ink">Recompensas disponibles</h2>
        <ul class="mt-3 space-y-2">
            @foreach ($availableRewards as $reward)
                <li class="flex items-center justify-between gap-3 rounded-xl border border-primary/40 bg-primary-tint p-3">
                    <span class="font-semibold">{{ $reward->name }}</span>
                    <span class="text-sm font-semibold text-primary">Lista para canjear</span>
                </li>
            @endforeach
        </ul>
    @elseif ($member->unitsToNextReward() !== null)
        <p class="mt-5 rounded-xl border border-line p-3 text-center text-sm text-ink-soft">
            Te faltan <strong class="text-ink">{{ number_format($member->unitsToNextReward(), 0, ',', '.') }} {{ $program->unit_name }}</strong> para tu próxima recompensa.
        </p>
    @endif

    @if ($rewards->isNotEmpty())
        <h2 class="mt-6 text-sm font-bold uppercase tracking-wider text-ink">Próximas recompensas</h2>
        <ul class="mt-3 space-y-2">
            @foreach ($rewards as $reward)
                <li class="flex items-center justify-between gap-3 rounded-xl border border-line p-3">
                    <span>{{ $reward->name }}</span>
                    <span class="text-sm text-ink-soft">{{ $reward->requirementDisplay() }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</x-module-page>
