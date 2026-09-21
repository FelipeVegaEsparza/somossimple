@php
    $status = $ticket->status();
    $valid = $ticket->isUsable();
    $appleEnabled = (bool) config('ticketera.wallet.apple.enabled');
    $googleEnabled = (bool) config('ticketera.wallet.google.enabled');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrada {{ $ticket->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app">
    <div class="max-w-lg mx-auto min-h-screen bg-surface shadow-card pb-10">
        <div class="rounded-b-3xl p-6 text-white text-center" style="background: linear-gradient(150deg, #437eff, #1a2233);">
            <p class="text-xs font-semibold uppercase tracking-widest text-white/70">{{ $ticket->event?->name }}</p>
            <p class="mt-2 text-2xl font-extrabold tracking-tight">{{ $ticket->ticketType?->name }}</p>
            <p class="mt-1 text-sm text-white/80">
                {{ $ticket->event?->starts_at->translatedFormat('d M Y · H:i') }}
            </p>
        </div>

        <div class="p-5">
            @if (! $valid)
                <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">
                    Entrada {{ strtolower($status->label()) }}. No permite el ingreso.
                </div>
            @endif

            <div class="flex flex-col items-center">
                <div class="rounded-2xl border border-line bg-white p-3">
                    <img src="{{ route('ticketera.ticket.qr', $ticket->token) }}" alt="QR {{ $ticket->number }}" class="w-56 h-56">
                </div>
                <p class="mt-2 font-mono text-sm text-ink-soft">{{ $ticket->token }}</p>
                <a href="{{ route('ticketera.ticket.qr', $ticket->token) }}" download="entrada-{{ $ticket->number }}.svg" class="btn-secondary mt-3">Descargar QR</a>
            </div>

            <div class="mt-6 rounded-xl border border-line p-4 text-sm">
                <div class="flex justify-between"><span class="text-ink-soft">Titular</span><span class="font-medium">{{ $ticket->holder_name }}</span></div>
                <div class="mt-1 flex justify-between"><span class="text-ink-soft">Entrada</span><span class="font-mono">{{ $ticket->number }}</span></div>
                <div class="mt-1 flex justify-between"><span class="text-ink-soft">Estado</span><span class="badge {{ $status->badge() }}">{{ $status->label() }}</span></div>
                @if ($ticket->used_at)
                    <div class="mt-1 flex justify-between"><span class="text-ink-soft">Ingreso</span><span>{{ $ticket->used_at->format('d/m/Y H:i') }}</span></div>
                @endif
            </div>

            @if ($ticket->event?->venue_name || $ticket->event?->venue_address)
                <p class="mt-4 text-sm text-ink-soft">📍 {{ $ticket->event->venue_name }}@if ($ticket->event->venue_address) · {{ $ticket->event->venue_address }}@endif</p>
            @endif
            @if ($ticket->event?->organizer)
                <p class="mt-1 text-sm text-ink-soft">Organiza {{ $ticket->event->organizer }}</p>
            @endif

            <div class="mt-6">
                <h2 class="text-sm font-bold uppercase tracking-wider text-ink">Guardar en tu teléfono</h2>
                <div class="mt-3 space-y-2 text-sm">
                    @if ($appleEnabled)
                        <a href="#" class="btn-secondary w-full">Agregar a Apple Wallet</a>
                    @else
                        <p class="rounded-xl border border-line px-4 py-3 text-ink-soft">Apple Wallet aún no está configurado.</p>
                    @endif
                    @if ($googleEnabled)
                        <a href="#" class="btn-secondary w-full">Agregar a Google Wallet</a>
                    @else
                        <p class="rounded-xl border border-line px-4 py-3 text-ink-soft">Google Wallet aún no está configurado.</p>
                    @endif
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-ink-faint">
                Entrada gestionada con <a href="{{ route('landing.home') }}" class="font-semibold text-ink-soft">{{ config('app.name') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
