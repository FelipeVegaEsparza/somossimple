@php
    $member = $member ?? null;
    $qrUrl = $qrUrl ?? null;
    $primary = $program->card_primary_color ?: '#437eff';
    $secondary = $program->card_secondary_color ?: '#1a2233';
    $balance = $member ? $member->balanceFor($program->type()) : 0;
    $programName = $program->name ?: 'Programa de fidelización';
    $headline = $program->card_text_primary ?: $business->name;
    $footnote = $program->card_text_secondary ?: 'Presenta esta tarjeta en el negocio';
@endphp

<div class="relative w-full max-w-sm rounded-2xl p-5 text-white shadow-card overflow-hidden"
     style="background: linear-gradient(150deg, {{ $primary }}, {{ $secondary }})">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            @if ($business->logo_path)
                <img src="{{ asset('storage/'.$business->logo_path) }}" alt="" class="w-11 h-11 rounded-xl object-cover bg-white/20 shrink-0">
            @else
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white/20 font-bold text-lg shrink-0">{{ \Illuminate\Support\Str::substr($business->name, 0, 1) }}</span>
            @endif
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-white/70 truncate">{{ $programName }}</p>
                <p class="font-bold truncate">{{ $headline }}</p>
            </div>
        </div>
        <span class="text-[11px] font-semibold text-white/70 shrink-0">{{ $business->name }}</span>
    </div>

    <div class="mt-6 flex items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs text-white/70 truncate">{{ $member?->name ?? 'Nombre del cliente' }}</p>
            <p class="mt-1 text-3xl font-extrabold leading-none">
                {{ number_format($balance, 0, ',', '.') }}
                <span class="text-sm font-semibold text-white/80">{{ $program->unit_name }}</span>
            </p>
            <p class="mt-3 text-[11px] text-white/70 truncate">{{ $footnote }}</p>
        </div>

        <div class="shrink-0 rounded-xl bg-white p-1.5">
            @if ($qrUrl)
                <img src="{{ $qrUrl }}" alt="Código QR de {{ $member?->code }}" class="w-16 h-16">
            @else
                <svg viewBox="0 0 100 100" class="w-16 h-16" aria-hidden="true">
                    <rect x="6" y="6" width="26" height="26" fill="none" stroke="#111110" stroke-width="7"/>
                    <rect x="68" y="6" width="26" height="26" fill="none" stroke="#111110" stroke-width="7"/>
                    <rect x="6" y="68" width="26" height="26" fill="none" stroke="#111110" stroke-width="7"/>
                    <rect x="44" y="44" width="12" height="12" fill="#111110"/>
                    <rect x="62" y="44" width="8" height="8" fill="#111110"/>
                    <rect x="44" y="62" width="8" height="8" fill="#111110"/>
                    <rect x="78" y="62" width="16" height="8" fill="#111110"/>
                    <rect x="62" y="78" width="8" height="16" fill="#111110"/>
                </svg>
            @endif
        </div>
    </div>

    <p class="mt-4 font-mono text-[11px] tracking-wider text-white/80">{{ $member?->code ?? 'CLI-000001' }}</p>
</div>
