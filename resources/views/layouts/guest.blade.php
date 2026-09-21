<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        {{-- Panel de marca --}}
        <aside class="hidden lg:flex flex-col justify-between bg-ink text-white p-12">
            <a href="{{ route('landing.home') }}" class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white text-ink text-sm font-bold">S</span>
                <span class="text-lg font-bold tracking-tight">SomosSimple<span class="text-primary">.cl</span></span>
            </a>

            <div>
                <h2 class="text-4xl font-extrabold tracking-tight leading-[1.05]">La plataforma digital<br>de tu negocio.</h2>
                <ul class="mt-8 space-y-3 text-white/75">
                    <li class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Perfil digital gratis, siempre disponible</li>
                    <li class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Catálogo, reservas, clientes y comunicaciones</li>
                    <li class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Acceso físico con QR y NFC</li>
                </ul>

                <div class="mt-10 max-w-xs rounded-2xl bg-white/5 border border-white/10 p-5 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-white grid place-items-center shrink-0">
                        <svg viewBox="0 0 100 100" class="w-12 h-12" aria-hidden="true">
                            <rect x="6" y="6" width="26" height="26" fill="none" stroke="#111110" stroke-width="7"/>
                            <rect x="68" y="6" width="26" height="26" fill="none" stroke="#111110" stroke-width="7"/>
                            <rect x="6" y="68" width="26" height="26" fill="none" stroke="#111110" stroke-width="7"/>
                            <rect x="44" y="44" width="12" height="12" fill="#111110"/>
                            <rect x="62" y="44" width="8" height="8" fill="#111110"/>
                            <rect x="44" y="62" width="8" height="8" fill="#111110"/>
                            <rect x="78" y="62" width="16" height="8" fill="#111110"/>
                            <rect x="62" y="78" width="8" height="16" fill="#111110"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Un toque o escaneo</p>
                        <p class="text-sm text-white/60 mt-0.5">para entrar al negocio digital.</p>
                    </div>
                </div>
            </div>

            <p class="text-xs text-white/40">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </aside>

        {{-- Contenido --}}
        <main class="min-h-screen flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <a href="{{ route('landing.home') }}" class="lg:hidden flex items-center justify-center gap-2 mb-8">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-ink text-white text-sm font-bold">S</span>
                    <span class="text-lg font-bold tracking-tight">SomosSimple<span class="text-primary">.cl</span></span>
                </a>
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
