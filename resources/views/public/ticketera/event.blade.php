@php
    $isCancelled = $event->status() === \App\Enums\TicketeraEventStatus::Cancelled;
    $isPaused = $event->status() === \App\Enums\TicketeraEventStatus::Paused;
    $onSale = $event->isPublished() && ! $isCancelled;
    $priceMap = $ticketTypes->mapWithKeys(fn ($t) => [$t->id => $t->price]);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->name }}</title>
    @include('public._meta', [
        'business' => $event->business,
        'title' => $event->name,
        'description' => $event->description,
        'image' => $event->image_path ? asset('storage/'.$event->image_path) : null,
    ])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app">
    <div class="max-w-lg mx-auto min-h-screen bg-surface shadow-card pb-12">
        <div class="relative">
            @if ($event->image_path)
                <img src="{{ asset('storage/'.$event->image_path) }}" alt="{{ $event->name }}" class="w-full h-56 object-cover">
            @else
                <div class="w-full h-56" style="background: linear-gradient(150deg, #437eff, #1a2233);"></div>
            @endif
            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,.75), rgba(0,0,0,.1) 60%);"></div>
            <div class="absolute inset-x-0 bottom-0 p-5">
                @if ($event->category)
                    <p class="text-xs font-semibold uppercase tracking-widest text-white/70">{{ $event->category }}</p>
                @endif
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">{{ $event->name }}</h1>
            </div>
            @if ($isCancelled)
                <span class="absolute top-4 left-4 rounded-full bg-danger px-3 h-8 inline-flex items-center text-xs font-bold text-white">EVENTO CANCELADO</span>
            @endif
        </div>

        <div class="p-5">
            <div class="space-y-2 text-sm text-ink-soft">
                <p class="flex items-center gap-2"><span class="text-ink-faint">📅</span>{{ $event->starts_at->translatedFormat('l d \d\e F · H:i') }}@if ($event->ends_at) – {{ $event->ends_at->format('H:i') }}@endif</p>
                @if ($event->venue_name || $event->venue_address)
                    <p class="flex items-start gap-2"><span class="text-ink-faint">📍</span><span>{{ $event->venue_name }}@if ($event->venue_address) · {{ $event->venue_address }}@endif @if ($event->venue_city) · {{ $event->venue_city }}@endif</span></p>
                @endif
                @if ($event->organizer)
                    <p class="flex items-center gap-2"><span class="text-ink-faint">🎫</span>Organiza {{ $event->organizer }}</p>
                @endif
            </div>

            @if ($event->description)
                <p class="mt-4 text-[15px] leading-relaxed text-ink">{{ $event->description }}</p>
            @endif

            @if ($event->venue_info)
                <p class="mt-3 rounded-xl bg-app px-4 py-3 text-sm text-ink-soft">{{ $event->venue_info }}</p>
            @endif

            <h2 class="mt-6 text-sm font-bold uppercase tracking-wider text-ink">Entradas</h2>
            @if ($ticketTypes->isEmpty())
                <p class="mt-2 text-sm text-ink-soft">Aún no hay entradas publicadas.</p>
            @else
                <div class="mt-3 space-y-2">
                    @foreach ($ticketTypes as $type)
                        @php($available = $type->isOnSale())
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-line p-3">
                            <div class="min-w-0">
                                <p class="font-semibold">{{ $type->name }}</p>
                                @if ($type->description)<p class="text-xs text-ink-soft">{{ $type->description }}</p>@endif
                                <p class="text-xs text-ink-faint">{{ $type->remaining() }} disponibles</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-extrabold">{{ $type->priceDisplay() }}</p>
                                @if (! $available)
                                    <span class="badge bg-app text-ink-soft">Agotado</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($isCancelled)
                <div class="mt-6 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">Este evento fue cancelado. Si compraste una entrada, contacta al organizador.</div>
            @elseif ($isPaused)
                <div class="mt-6 rounded-xl border border-line bg-app px-4 py-3 text-sm text-ink-soft">Las ventas están pausadas por el organizador.</div>
            @elseif ($ticketTypes->where(fn ($t) => $t->isOnSale())->isEmpty())
                <div class="mt-6 rounded-xl border border-line bg-app px-4 py-3 text-sm text-ink-soft">Las entradas están agotadas.</div>
            @endif

            @if ($onSale && $ticketTypes->isNotEmpty())
                @php($firstOnSale = $ticketTypes->first(fn ($t) => $t->isOnSale()))
                @if ($firstOnSale)
                    <form method="POST" action="{{ route('ticketera.checkout', $event->slug) }}" class="mt-6 space-y-4"
                          x-data="{ type: @js((string) $firstOnSale->id), qty: 1, prices: @js($priceMap), fmt(n){ return '$' + n.toLocaleString('es-CL'); } }">
                        @csrf
                        @if (session('error'))
                            <div class="rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
                        @endif

                        <div>
                            <label for="ticket_type_id" class="label">Tipo de entrada</label>
                            <select id="ticket_type_id" name="ticket_type_id" x-model="type" class="input" required>
                                @foreach ($ticketTypes as $type)
                                    @if ($type->isOnSale())
                                        <option value="{{ $type->id }}">{{ $type->name }} — {{ $type->priceDisplay() }} ({{ $type->remaining() }} disponibles)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="quantity" class="label">Cantidad</label>
                            <input id="quantity" type="number" name="quantity" min="1" max="20" x-model.number="qty" class="input w-28" required>
                        </div>

                        <div class="border-t border-line pt-4 space-y-3">
                            <p class="text-sm font-semibold">Tus datos</p>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nombre" class="input" required>
                                <input type="text" name="lastname" value="{{ old('lastname') }}" placeholder="Apellido" class="input">
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" class="input" required>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Teléfono (opcional)" class="input">
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-app px-4 py-3">
                            <span class="text-sm text-ink-soft">Total</span>
                            <span class="text-lg font-extrabold" x-text="fmt((prices[type] || 0) * (qty || 0))">$0</span>
                        </div>

                        <button type="submit" class="btn-primary w-full h-12 text-base">Comprar entrada</button>
                        <p class="text-center text-xs text-ink-faint">No necesitas crear una cuenta.</p>
                    </form>
                @endif
            @endif

            <p class="mt-8 text-center text-xs text-ink-faint">
                Entradas gestionadas con <a href="{{ route('landing.home') }}" class="font-semibold text-ink-soft">{{ config('app.name') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
