@props(['business', 'title', 'icon' => null, 'tagline' => null])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · {{ $business->name }}</title>
    @include('public._meta', ['business' => $business, 'title' => $title.' · '.$business->name])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('public._pwa', ['business' => $business])
</head>
<body class="bg-app theme-{{ $business->theme ?: 'clasico' }}">
    <div class="max-w-md mx-auto min-h-screen bg-surface pb-16 shadow-card">
        {{-- Encabezado del negocio --}}
        <div class="relative">
            @if ($business->cover_path)
                <img src="{{ asset('storage/'.$business->cover_path) }}" alt="" class="w-full h-32 object-cover">
            @else
                <div class="w-full h-32 bg-strong"></div>
            @endif

            <a href="{{ route('p.show', $business->slug) }}" class="absolute top-4 left-4 inline-flex items-center gap-1.5 rounded-full bg-surface/90 backdrop-blur px-3 h-9 text-xs font-semibold text-ink shadow-soft">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Perfil
            </a>
        </div>

        <div class="px-5">
            <div class="relative z-10 -mt-8 flex items-center gap-3">
                @if ($business->logo_path)
                    <img src="{{ asset('storage/'.$business->logo_path) }}" alt="Logo de {{ $business->name }}" class="w-16 h-16 object-cover rounded-2xl border-4 border-surface bg-surface shadow-card">
                @else
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl border-4 border-surface bg-app text-ink font-bold text-xl shadow-card">{{ Str::substr($business->name, 0, 1) }}</span>
                @endif
                <a href="{{ route('p.show', $business->slug) }}" class="font-bold text-ink hover:text-primary">{{ $business->name }}</a>
            </div>

            {{-- Título del módulo --}}
            <div class="mt-6 flex items-center gap-3">
                @if ($icon)
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-strong text-white shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                        </svg>
                    </span>
                @endif
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-ink">{{ $title }}</h1>
                    @if ($tagline)
                        <p class="text-sm text-ink-soft">{{ $tagline }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-5">{{ $slot }}</div>

            @if ($business->whatsapp)
                @php($wa = preg_replace('/[^0-9]/', '', $business->whatsapp))
                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                   class="mt-8 flex items-center justify-center gap-2 h-12 rounded-xl text-white font-semibold text-sm" style="background-color:#25D366">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2Zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.26 8.26 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.54-3.7 8.24-8.24 8.24Z"/></svg>
                    Escríbenos por WhatsApp
                </a>
            @endif

            <p class="mt-8 pb-4 text-center text-xs text-ink-faint">
                Perfil digital creado con <a href="{{ route('landing.home') }}" class="font-semibold text-ink-soft hover:text-ink">{{ config('app.name') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
