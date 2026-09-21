@php
    $rule = match ($program->type()) {
        \App\Enums\LoyaltyProgramType::Points, \App\Enums\LoyaltyProgramType::Rewards => '$'.number_format($program->earn_amount ?? 1000, 0, ',', '.').' = '.number_format($program->earn_units, 0, ',', '.').' '.$program->unit_name,
        \App\Enums\LoyaltyProgramType::Visits => 'Cada visita suma 1 '.$program->unit_name,
        \App\Enums\LoyaltyProgramType::Stamps => 'Cada compra suma 1 '.$program->unit_name,
    };
@endphp

<x-module-page :business="$business" title="Fidelización" :icon="\App\Enums\Module::Loyalty->icon()" :tagline="$program->name">
    @if ($program->description)
        <p class="text-sm leading-relaxed text-ink-soft">{{ $program->description }}</p>
    @endif

    <div class="mt-4 rounded-xl border border-line p-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-ink-faint">Cómo funciona</p>
        <p class="mt-1 text-sm font-medium text-ink">{{ $rule }}</p>
    </div>

    @if ($rewards->isNotEmpty())
        <h2 class="mt-6 text-sm font-bold uppercase tracking-wider text-ink">Recompensas</h2>
        <ul class="mt-3 space-y-2">
            @foreach ($rewards as $reward)
                <li class="flex items-center justify-between gap-3 rounded-xl border border-line p-3">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $reward->name }}</p>
                        @if ($reward->description)
                            <p class="text-sm text-ink-soft">{{ $reward->description }}</p>
                        @endif
                    </div>
                    <span class="shrink-0 text-sm font-semibold text-primary">{{ $reward->requirementDisplay() }}</span>
                </li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('loyalty.register', $business->slug) }}" class="btn-primary w-full h-12 mt-6">Registrarme y obtener mi tarjeta</a>

    <form method="POST" action="{{ route('loyalty.identify', $business->slug) }}" class="mt-4 space-y-2">
        @csrf
        <label for="identifier" class="label">¿Ya tienes tarjeta?</label>
        <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" class="input" placeholder="Tu código CLI-000001, teléfono o correo" required>
        @error('identifier') <p class="error-msg">{{ $message }}</p> @enderror
        <button type="submit" class="btn-secondary w-full">Ver mi tarjeta</button>
    </form>
</x-module-page>
