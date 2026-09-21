<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compra {{ $order->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app">
    <div class="max-w-lg mx-auto min-h-screen bg-surface shadow-card p-5">
        <div class="rounded-xl bg-primary-tint px-4 py-3 text-sm text-ink">
            ¡Listo! Tu compra quedó registrada con el número <strong class="font-mono">{{ $order->number }}</strong>.
        </div>

        <h1 class="mt-5 text-2xl font-extrabold tracking-tight">{{ $event->name }}</h1>
        <p class="mt-1 text-sm text-ink-soft">
            {{ $event->starts_at->translatedFormat('l d \d\e F · H:i') }}
            @if ($event->venue_name) · {{ $event->venue_name }}@endif
        </p>

        <div class="mt-5 rounded-xl border border-line p-4">
            <div class="flex justify-between text-sm"><span class="text-ink-soft">Comprador</span><span class="font-medium">{{ $order->buyerFullName() }}</span></div>
            <div class="mt-1 flex justify-between text-sm"><span class="text-ink-soft">Correo</span><span>{{ $order->buyer_email }}</span></div>
            <div class="mt-1 flex justify-between text-sm"><span class="text-ink-soft">Total</span><span class="font-semibold">${{ number_format($order->total, 0, ',', '.') }}</span></div>
            <div class="mt-1 flex justify-between text-sm"><span class="text-ink-soft">Estado</span><span class="badge {{ $order->status()->badge() }}">{{ $order->status()->label() }}</span></div>
        </div>

        <h2 class="mt-6 text-sm font-bold uppercase tracking-wider text-ink">Tus entradas ({{ $order->tickets->count() }})</h2>
        <div class="mt-3 space-y-2">
            @foreach ($order->tickets as $ticket)
                <a href="{{ route('ticketera.ticket', $ticket->token) }}" class="flex items-center justify-between gap-3 rounded-xl border border-line p-3 hover:border-primary/40">
                    <div>
                        <p class="font-semibold">{{ $ticket->ticketType?->name }}</p>
                        <p class="font-mono text-xs text-ink-soft">{{ $ticket->number }}</p>
                    </div>
                    <span class="text-sm font-semibold text-primary">Ver entrada</span>
                </a>
            @endforeach
        </div>

        <p class="mt-5 text-xs text-ink-faint">
            Guarda este enlace o recupera tus entradas desde <a href="{{ route('ticketera.recover') }}" class="font-semibold text-ink-soft">Recuperar entradas</a> con tu correo y número de compra.
        </p>
    </div>
</body>
</html>
