@foreach ($reservations as $reservation)
    <div class="card p-4 mb-3 flex items-center gap-4">
        <span class="text-sm font-bold w-12 shrink-0">{{ $reservation->starts_at->format('H:i') }}</span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold">{{ $reservation->client_name }}</p>
            <p class="text-sm text-ink-soft">{{ $reservation->service_name }} · {{ $reservation->duration_minutes }} min</p>
            <p class="text-xs text-ink-faint">{{ $reservation->phone }}{{ $reservation->email ? ' · '.$reservation->email : '' }}</p>
        </div>
        <span class="badge @php
            echo match ($reservation->status) {
                'confirmed' => 'bg-primary-tint text-primary',
                'cancelled' => 'bg-red-50 text-danger',
                'completed' => 'bg-[#e6f6ee] text-good',
                default => 'bg-[#fff6e0] text-warn',
            };
        @endphp">{{ $statusLabels[$reservation->status] }}</span>
        @php
            $allowed = match ($reservation->status) {
                'pending' => ['confirmed' => 'Confirmar', 'cancelled' => 'Cancelar'],
                'confirmed' => ['completed' => 'Completar', 'cancelled' => 'Cancelar'],
                default => [],
            };
        @endphp
        <div class="flex gap-1.5 shrink-0">
            @foreach ($allowed as $value => $label)
                <form method="POST" action="{{ route('panel.reservations.update-status', $reservation) }}">
                    @csrf
                    <input type="hidden" name="status" value="{{ $value }}">
                    <button type="submit" class="text-xs font-semibold text-primary hover:underline">{{ $label }}</button>
                </form>
            @endforeach
        </div>
    </div>
@endforeach
