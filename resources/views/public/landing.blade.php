<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Soluciones simples para tu negocio</title>
    <meta name="description" content="Crea gratis tu Perfil Digital y reúne todo lo que tus clientes necesitan saber de tu negocio. Después activa catálogo, menú, reservas, clientes y más. Acceso por QR, NFC o enlace.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    @php
        $precios = collect($modulosPago)->mapWithKeys(fn ($m) => [$m['module']->value => $m['price']]);

        $iconos = [
            'catalog' => 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z',
            'menu' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
            'reservations' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
            'clients' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
            'communications' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
            'promotions' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z',
            'services' => 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26',
            'events' => 'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 4.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-4.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z',
            'gallery' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z',
            'loyalty' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z',
            'ticketera' => 'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 4.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-4.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z',
        ];

        $soluciones = [
            ['key' => 'catalog', 'titulo' => 'Catálogo', 'problema' => 'Mis clientes me preguntan constantemente qué vendo.', 'mensaje' => 'Muestra productos, servicios, cartas o tarifas ordenados y con precios. Accesible desde tu QR, NFC o Perfil Digital.', 'cta' => 'Quiero mostrar mis productos'],
            ['key' => 'menu', 'titulo' => 'Menú', 'problema' => 'Necesito que mis clientes vean mi carta fácilmente.', 'mensaje' => 'Tu menú siempre actualizado y disponible desde el teléfono de tus clientes. Cambia platos o precios sin reimprimir.', 'cta' => 'Quiero crear mi menú digital'],
            ['key' => 'reservations', 'titulo' => 'Reservas', 'problema' => 'Estoy perdiendo tiempo coordinando reservas por mensajes.', 'mensaje' => 'Tus clientes reservan cuando quieran, sin conversaciones interminables. Tú solo confirmas.', 'cta' => 'Quiero recibir reservas'],
            ['key' => 'clients', 'titulo' => 'Clientes', 'problema' => 'Mis clientes están repartidos entre WhatsApp y libretas.', 'mensaje' => 'Tus clientes ordenados y siempre a mano. Registra, consulta y reconoce a quienes más vuelven.', 'cta' => 'Quiero organizar mis clientes'],
            ['key' => 'communications', 'titulo' => 'Comunicaciones', 'problema' => 'Quiero avisar a mis clientes cuando tengo novedades.', 'mensaje' => 'Mantén el contacto: promociones, ofertas, anuncios y comunicaciones importantes en un solo lugar.', 'cta' => 'Quiero comunicarme con mis clientes'],
            ['key' => 'promotions', 'titulo' => 'Promociones', 'problema' => 'Tengo ofertas, pero mis clientes no siempre se enteran.', 'mensaje' => 'Haz que tus promociones estén donde tus clientes ya están mirando: tu Perfil Digital y tus puntos QR/NFC.', 'cta' => 'Quiero crear una promoción'],
            ['key' => 'services', 'titulo' => 'Servicios', 'problema' => 'Mis clientes quieren saber qué hago y cuánto cuesta.', 'mensaje' => 'Explica tus servicios antes de que te pregunten: descripción, precios, duración y fotografías.', 'cta' => 'Quiero mostrar mis servicios'],
            ['key' => 'events', 'titulo' => 'Eventos', 'problema' => 'Quiero dar a conocer mis actividades.', 'mensaje' => 'Publica eventos, cursos, talleres y lanzamientos. Tus clientes acceden desde un QR o NFC.', 'cta' => 'Quiero mostrar mis eventos'],
            ['key' => 'gallery', 'titulo' => 'Galería', 'problema' => 'Quiero que vean lo que hago antes de decidir.', 'mensaje' => 'Muestra tu negocio en imágenes: trabajos, productos, espacios y resultados.', 'cta' => 'Quiero mostrar mi galería'],
            ['key' => 'loyalty', 'titulo' => 'Fidelización', 'problema' => 'Mis clientes vienen una vez y no vuelven.', 'mensaje' => 'Crea un programa de puntos, visitas o sellos y entrega a cada cliente su tarjeta digital con QR, Apple Wallet y Google Wallet.', 'cta' => 'Quiero fidelizar a mis clientes'],
            ['key' => 'ticketera', 'titulo' => 'Ticketera', 'problema' => 'Quiero vender entradas para mi evento sin complicarme.', 'mensaje' => 'Crea tu evento, vende entradas online y entrega a cada comprador su entrada digital con QR. Valida el acceso desde el teléfono.', 'cta' => 'Quiero vender entradas'],
        ];

        $necesidades = [
            ['Quiero mostrar mis productos', 'Catálogo'],
            ['Quiero mostrar mi menú', 'Menú'],
            ['Quiero recibir reservas', 'Reservas'],
            ['Quiero organizar mis clientes', 'Clientes'],
            ['Quiero publicar promociones', 'Promociones'],
            ['Quiero mostrar mis servicios', 'Servicios'],
            ['Quiero fidelizar a mis clientes', 'Fidelización'],
            ['Quiero vender entradas para mi evento', 'Ticketera'],
            ['Quiero que encuentren mi negocio', 'Perfil Digital'],
            ['Quiero que accedan fácilmente', 'QR / NFC'],
        ];

        $rubros = ['Restaurantes', 'Cafeterías', 'Food trucks', 'Barberías', 'Peluquerías', 'Centros de estética', 'Tiendas', 'Emprendimientos', 'Profesionales', 'Servicios técnicos', 'Centros médicos', 'Alojamientos', 'Talleres', 'Otros negocios'];
    @endphp

    <header class="border-b border-line bg-white/90 backdrop-blur sticky top-0 z-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('landing.home') }}" class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-ink text-white text-sm font-bold">S</span>
                <span class="text-lg font-bold tracking-tight">SomosSimple<span class="text-primary">.cl</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-ink-soft">
                <a href="#como" class="hover:text-ink">Cómo funciona</a>
                <a href="#soluciones" class="hover:text-ink">Soluciones</a>
                <a href="#qrnfc" class="hover:text-ink">QR y NFC</a>
                <a href="#faq" class="hover:text-ink">Preguntas</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ auth()->user()->is_platform_admin ? route('admin.index') : route('panel.index') }}" class="btn-primary">Ir a mi panel</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn-primary">Crear mi Perfil Digital gratis</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{-- HERO --}}
        <section class="py-14 sm:py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 grid lg:grid-cols-[1.05fr_0.95fr] gap-12 items-center">
                <div>
                    <p class="inline-flex items-center rounded-full bg-primary-tint text-primary px-3.5 h-8 text-xs font-semibold">Para negocios, emprendimientos y profesionales</p>
                    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight leading-[1.05]">
                        Tu negocio tiene necesidades. Nosotros tenemos una <span class="text-primary">solución simple</span> para cada una.
                    </h1>
                    <p class="mt-6 text-lg text-ink-soft leading-relaxed max-w-xl">
                        Crea gratis tu <strong class="text-ink">Perfil Digital</strong> y reúne en un solo lugar todo lo que tus clientes necesitan saber de tu negocio. Después agrega las soluciones que realmente necesitas: catálogo, menú, reservas, clientes, promociones y más.
                    </p>
                    <p class="mt-4 text-lg text-ink-soft leading-relaxed max-w-xl">
                        Tus clientes acceden fácilmente desde un <strong class="text-ink">QR, NFC o enlace</strong>.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn-primary h-13 px-7 text-base">Crear mi Perfil Digital gratis</a>
                        <a href="#soluciones" class="btn-secondary h-13 px-7 text-base">Ver soluciones</a>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-soft">
                        <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-good"></span> Perfil Digital gratis</span>
                        <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-good"></span> Sin tarjeta de crédito</span>
                        <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-good"></span> Sin conocimientos técnicos</span>
                    </div>
                </div>

                {{-- Composición visual --}}
                <div class="relative">
                    <div class="absolute inset-x-6 top-6 bottom-0 rounded-3xl bg-primary/10"></div>
                    <div class="relative rounded-2xl bg-ink text-white p-6 shadow-card">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-widest text-white/50">Acceso fácil</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5 text-white/60"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z"/></svg>
                        </div>
                        <div class="mt-4 flex items-center gap-4">
                            <div class="w-20 h-20 rounded-xl bg-white grid place-items-center shrink-0">
                                <svg viewBox="0 0 100 100" class="w-14 h-14" aria-hidden="true">
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
                                <p class="font-bold">Un toque o un escaneo</p>
                                <p class="text-sm text-white/60 mt-1">Tus clientes llegan directo a tu perfil, tu menú o tus reservas. Sin instalar nada.</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative -mt-6 ml-6 rounded-2xl bg-white border border-line shadow-card p-5">
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-xl bg-ink text-white grid place-items-center font-bold">P</span>
                            <div>
                                <p class="font-bold">Barbería Patagonia</p>
                                <p class="text-xs text-ink-soft">Barbería en Chile Chico.</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <span class="rounded-lg bg-app py-2 text-xs font-semibold">WhatsApp</span>
                            <span class="rounded-lg bg-app py-2 text-xs font-semibold">Llamar</span>
                            <span class="rounded-lg bg-app py-2 text-xs font-semibold">Ubicación</span>
                        </div>
                        <div class="mt-2 rounded-lg bg-primary py-2.5 text-center text-xs font-semibold text-white">Reservar</div>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-6xl px-4 sm:px-6 mt-14">
                <p class="text-center text-xs font-bold uppercase tracking-widest text-ink-faint">Sirve para tu rubro</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    @foreach (['Barberías', 'Restaurantes', 'Cafeterías', 'Peluquerías', 'Tiendas', 'Centros de estética', 'Servicios técnicos', 'Profesionales', 'Alojamientos'] as $rubro)
                        <span class="rounded-full border border-line px-3.5 h-9 inline-flex items-center text-sm font-medium text-ink-soft">{{ $rubro }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CREAR PERFIL DIGITAL GRATIS --}}
        <section class="py-12 bg-primary-tint">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Crea tu Perfil Digital gratis</h2>
                    <p class="mt-1 text-ink-soft">Todo lo importante de tu negocio en un solo lugar. Sin tarjeta de crédito y sin compromiso.</p>
                </div>
                <a href="{{ route('register') }}" class="btn-primary h-13 px-7 text-base shrink-0">Crear mi Perfil Digital gratis</a>
            </div>
        </section>

        {{-- ¿QUÉ NECESITAS RESOLVER? --}}
        <section id="necesidades" class="py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center">¿Qué necesitas resolver?</h2>
                <p class="text-center text-ink-soft mt-2 max-w-xl mx-auto">Empieza por tu necesidad, no por la tecnología. Toca la que se parezca a la tuya.</p>

                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach ($necesidades as $n)
                        <a href="#soluciones" class="card p-5 hover:border-primary/50 transition-colors">
                            <p class="font-semibold">{{ $n[0] }}</p>
                            <p class="mt-2 text-sm text-primary font-semibold">→ {{ $n[1] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- COMO FUNCIONA --}}
        <section id="como" class="py-16 bg-app">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center">Cómo funciona SomosSimple</h2>
                <p class="text-center text-ink-soft mt-2 max-w-xl mx-auto">No necesitas contratar un sistema gigante. Empiezas con lo esencial y creces cuando lo necesites.</p>

                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ([
                        ['Crea', 'Crea gratis tu Perfil Digital con la información de tu negocio.'],
                        ['Comparte', 'Comparte tu perfil mediante un enlace, un QR o un NFC.'],
                        ['Resuelve', 'Activa las soluciones que resuelven tus necesidades reales.'],
                        ['Crece', 'Cuando tu negocio pida algo nuevo, agrega otra solución.'],
                    ] as $i => $paso)
                        <div class="card p-6 relative overflow-hidden">
                            <span class="absolute -top-3 right-4 text-7xl font-extrabold text-line select-none">{{ $i + 1 }}</span>
                            <div class="relative">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary-tint text-primary font-bold">{{ $i + 1 }}</span>
                                <h3 class="mt-4 font-bold text-lg">{{ $paso[0] }}</h3>
                                <p class="mt-2 text-ink-soft text-[15px]">{{ $paso[1] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex flex-wrap justify-center items-center gap-x-3 gap-y-2 text-sm font-semibold text-ink-soft">
                    @foreach (['Perfil Digital', 'Menú', 'Reservas', 'WhatsApp', 'Promociones'] as $i => $flujo)
                        @if ($i > 0)<span class="text-ink-faint">→</span>@endif
                        <span class="rounded-full bg-surface border border-line px-3.5 h-9 inline-flex items-center">{{ $flujo }}</span>
                    @endforeach
                    <span class="w-full text-center text-xs font-medium text-ink-faint mt-1">Todo desde un mismo lugar.</span>
                </div>
            </div>
        </section>

        {{-- PERFIL DIGITAL --}}
        <section id="perfil" class="py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="badge bg-primary-tint text-primary">Gratis · Sin tarjeta de crédito</span>
                    <h2 class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight">Tu Perfil Digital</h2>
                    <p class="mt-3 text-lg text-ink-soft">"Mis clientes no tienen dónde encontrar toda mi información."</p>
                    <p class="mt-3 text-ink-soft">Reúne en un solo lugar lo más importante de tu negocio y compártelo con una dirección digital propia. Es el punto de partida de todo lo demás.</p>

                    <ul class="mt-6 grid sm:grid-cols-2 gap-x-6 gap-y-2.5 text-[15px]">
                        @foreach ([
                            'Información del negocio', 'Logo y fotografía', 'Descripción', 'Dirección y ubicación',
                            'Horarios de atención', 'Teléfono y WhatsApp', 'Redes sociales y sitio web', 'Botones de contacto',
                        ] as $feature)
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-good shrink-0"></span>{{ $feature }}</li>
                        @endforeach
                    </ul>

                    <p class="mt-6 text-lg font-semibold">Todo lo importante de tu negocio en un solo lugar.</p>
                    <a href="{{ route('register') }}" class="btn-primary h-13 px-7 text-base mt-6">Crear mi Perfil Digital gratis</a>
                </div>

                <div class="rounded-2xl bg-app p-6 sm:p-8">
                    <div class="mx-auto max-w-sm rounded-2xl bg-white border border-line shadow-card p-5">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-xl bg-ink text-white grid place-items-center text-lg font-bold">P</span>
                            <div>
                                <p class="font-bold">Barbería Patagonia</p>
                                <p class="text-sm text-ink-soft">Barbería en Chile Chico.</p>
                            </div>
                        </div>
                        <div class="mt-5 space-y-2">
                            <div class="rounded-lg bg-white border border-line px-4 py-3 text-sm font-semibold flex items-center gap-2.5"><span class="w-5 h-5 rounded bg-wa/15"></span> WhatsApp</div>
                            <div class="rounded-lg bg-white border border-line px-4 py-3 text-sm font-semibold flex items-center gap-2.5"><span class="w-5 h-5 rounded bg-ink/10"></span> Llamar</div>
                            <div class="rounded-lg bg-white border border-line px-4 py-3 text-sm font-semibold flex items-center gap-2.5"><span class="w-5 h-5 rounded bg-primary/15"></span> Ubicación</div>
                        </div>
                        <p class="mt-5 text-xs font-bold uppercase tracking-wider text-ink-faint">Servicios</p>
                        <div class="mt-2 rounded-lg bg-app px-4 py-3 flex justify-between text-sm"><span>Corte clásico</span><span class="font-semibold">$12.000</span></div>
                        <div class="mt-2 rounded-lg bg-app px-4 py-3 flex justify-between text-sm"><span>Corte + barba</span><span class="font-semibold">$18.000</span></div>
                        <div class="mt-4 rounded-lg bg-primary px-4 py-3.5 text-center text-sm font-semibold text-white">Reservar</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- QR Y NFC --}}
        <section id="qrnfc" class="py-16 bg-ink text-white">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 grid lg:grid-cols-2 gap-12 items-start">
                <div>
                    <p class="text-primary font-semibold text-sm uppercase tracking-widest">QR y NFC</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight">Tu negocio también puede estar a un toque de distancia.</h2>
                    <p class="mt-5 text-white/70 leading-relaxed text-lg">
                        Tus clientes acercan su teléfono o escanean un código y llegan directamente a tu información. El mismo negocio puede tener varios puntos de acceso.
                    </p>

                    <div class="mt-8 grid sm:grid-cols-2 gap-3">
                        @foreach ([
                            ['Puerta', 'Perfil Digital'],
                            ['Mesa', 'Menú'],
                            ['Mostrador', 'Promoción'],
                            ['Tarjeta', 'Contacto'],
                            ['Producto', 'Información'],
                            ['Escaparate', 'Catálogo'],
                        ] as $punto)
                            <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 flex items-center gap-3">
                                <span class="text-white/50 text-sm font-semibold w-20 shrink-0">{{ $punto[0] }}</span>
                                <span class="text-white/30">→</span>
                                <span class="font-semibold">{{ $punto[1] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-6 text-sm text-white/50">Un QR o un NFC puede llevar a cualquier solución activa de tu negocio.</p>
                </div>

                <div class="bg-surface text-ink rounded-2xl p-6 sm:p-8">
                    <h3 class="text-lg font-bold">¿Quieres tus accesos físicos?</h3>
                    <p class="text-sm text-ink-soft mt-1">Te contactamos para coordinar cantidad y entrega.@if ($kitPrice !== null) <span class="font-semibold text-ink">Costo único: ${{ number_format($kitPrice, 0, ',', '.') }} por kit</span>@endif</p>

                    <div class="mt-5 flex items-center gap-4">
                        <div class="w-20 rounded-xl bg-white border border-line p-2.5">
                            <svg viewBox="0 0 100 100" class="w-full h-auto" aria-hidden="true">
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
                        <div class="text-xs text-ink-soft">
                            <p class="text-ink font-semibold text-sm">Tótem de escritorio</p>
                            <p>QR + NFC · activación con serial</p>
                        </div>
                    </div>

                    @if (session('kit_ok'))
                        <div class="mt-4 rounded-xl bg-[#e6f6ee] px-4 py-3 text-sm text-ink">{{ session('kit_ok') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mt-4 rounded-xl bg-red-50 border border-danger/30 px-4 py-3 text-sm text-danger">
                            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('landing.kit.request') }}" class="mt-5 space-y-3">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-3">
                            <div>
                                <label class="label">Tu nombre</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="input" required>
                            </div>
                            <div>
                                <label class="label">Tu negocio (opcional)</label>
                                <input type="text" name="business_name" value="{{ old('business_name') }}" class="input">
                            </div>
                            <div>
                                <label class="label">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="input" placeholder="+56 9 …">
                            </div>
                            <div>
                                <label class="label">Correo</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="input">
                            </div>
                        </div>
                        <div>
                            <label class="label">Cantidad de accesos</label>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="50" class="input w-28">
                        </div>
                        <button type="submit" class="btn-primary w-full">Quiero mis accesos QR/NFC</button>
                    </form>
                </div>
            </div>
        </section>

        {{-- SOLUCIONES / MODULOS --}}
        <section id="soluciones" class="py-16 bg-app">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center">Activa solamente la solución que necesitas</h2>
                <p class="text-center text-ink-soft mt-2 max-w-2xl mx-auto">No pagues por funciones que no usas. Cada solución responde a un problema real de tu negocio.</p>

                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @foreach ($soluciones as $s)
                        @php($precio = $precios[$s['key']] ?? null)
                        <article class="group flex flex-col rounded-2xl border border-line bg-surface p-6 transition duration-200 hover:-translate-y-0.5 hover:border-ink/15 hover:shadow-card">
                            <div class="flex items-start justify-between gap-4">
                                <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-ink text-white shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos[$s['key']] ?? '' }}"/>
                                    </svg>
                                </span>

                                @if ($precio !== null)
                                    <div class="text-right">
                                        <p class="text-lg font-extrabold tracking-tight tabular-nums leading-none">${{ number_format($precio, 0, ',', '.') }}</p>
                                        <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-ink-faint">al mes</p>
                                    </div>
                                @else
                                    <span class="badge bg-app text-ink-soft">A consultar</span>
                                @endif
                            </div>

                            <h3 class="mt-5 text-lg font-bold tracking-tight">{{ $s['titulo'] }}</h3>

                            <div class="mt-4 flex-1 space-y-4">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-ink-faint">El problema</p>
                                    <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $s['problema'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-primary/70">La solución</p>
                                    <p class="mt-1 text-[15px] leading-relaxed text-ink">{{ $s['mensaje'] }}</p>
                                </div>
                            </div>

                            <a href="{{ route('register') }}" class="mt-6 inline-block text-sm font-semibold text-primary underline decoration-primary/30 underline-offset-4 transition-colors hover:decoration-primary">{{ $s['cta'] }}</a>
                        </article>
                    @endforeach
                </div>

                <p class="mt-8 text-center text-sm text-ink-faint">Los accesos físicos QR/NFC se adquieren aparte, como producto de costo único.</p>
            </div>
        </section>

        {{-- PARA CADA TIPO DE NEGOCIO --}}
        <section id="negocios" class="py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center">Para cada tipo de negocio</h2>
                <p class="text-center text-ink-soft mt-2 max-w-xl mx-auto">Cada negocio elige lo que necesita. Ninguno tiene que contratar todo.</p>

                <div class="mt-8 flex flex-wrap justify-center gap-2">
                    @foreach ($rubros as $rubro)
                        <span class="rounded-full border border-line px-3.5 h-9 inline-flex items-center text-sm font-medium text-ink-soft">{{ $rubro }}</span>
                    @endforeach
                </div>

                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ([
                        ['Restaurante', ['Perfil Digital', 'Menú', 'Promociones', 'Reservas']],
                        ['Barbería', ['Perfil Digital', 'Servicios', 'Reservas']],
                        ['Tienda', ['Perfil Digital', 'Catálogo', 'WhatsApp']],
                    ] as $caso)
                        <div class="card p-6">
                            <p class="text-xs font-bold uppercase tracking-widest text-ink-faint">Tengo un/una</p>
                            <h3 class="mt-1 font-bold text-lg">{{ $caso[0] }}</h3>
                            <p class="mt-3 text-sm text-ink-soft">Necesito:</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($caso[1] as $modulo)
                                    <span class="rounded-lg bg-primary-tint text-primary px-3 h-8 inline-flex items-center text-sm font-semibold">{{ $modulo }}</span>
                                @endforeach
                            </div>
                            <p class="mt-4 text-sm font-semibold text-good">Listo.</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- EJEMPLOS DE USO --}}
        <section id="ejemplos" class="py-16 bg-app">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center">Un código, muchas puertas de entrada</h2>
                <p class="text-center text-ink-soft mt-2 max-w-xl mx-auto">Así usan SomosSimple distintos negocios todos los días.</p>

                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ([
                        ['Restaurante', 'Un cliente escanea el QR de la mesa y ve el menú actualizado.'],
                        ['Barbería', 'Un cliente acerca su teléfono al NFC y reserva su hora.'],
                        ['Tienda', 'Un cliente escanea el QR de la vitrina y contacta por WhatsApp.'],
                        ['Profesional', 'Un cliente acerca su teléfono a la tarjeta y guarda tus datos.'],
                    ] as $ejemplo)
                        <div class="card p-5">
                            <p class="font-bold">{{ $ejemplo[0] }}</p>
                            <p class="mt-2 text-sm text-ink-soft">{{ $ejemplo[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- EMPIEZA GRATIS --}}
        <section id="gratis" class="py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 grid lg:grid-cols-[1fr_1fr] gap-10 items-center">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Empieza gratis. Crece cuando lo necesites.</h2>
                    <p class="mt-3 text-ink-soft">No necesitas contratar un plan para comenzar. Crea tu Perfil Digital y comienza a tener presencia digital para tu negocio. Cuando aparezca una nueva necesidad, simplemente activa la solución correspondiente.</p>
                    <a href="{{ route('register') }}" class="btn-primary h-13 px-7 text-base mt-6">Crear mi Perfil Digital gratis</a>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach ([
                        'Perfil Digital gratis',
                        'Sin conocimientos técnicos',
                        'Sin complicaciones',
                        'Comienza en pocos minutos',
                        'Agrega soluciones cuando las necesites',
                        'Acceso por QR, NFC o enlace',
                    ] as $beneficio)
                        <div class="rounded-2xl border border-line bg-white p-4 flex gap-3">
                            <span class="w-1 rounded-full bg-good shrink-0"></span>
                            <span class="text-[15px] font-medium">{{ $beneficio }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CONFIANZA --}}
        <section class="py-14 bg-ink text-white">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 text-center">
                <p class="text-xl sm:text-2xl font-bold leading-relaxed">No necesitas ser experto en tecnología.</p>
                <p class="mt-3 text-lg text-white/70">Si sabes qué necesita tu negocio, nosotros te ayudamos a encontrar una solución simple.</p>
                <p class="mt-6 text-white/50">Tú conoces tu negocio. Nosotros hacemos más simple la parte digital.</p>
            </div>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="py-16">
            <div class="mx-auto max-w-3xl px-4 sm:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center">Preguntas frecuentes</h2>
                <div class="mt-8 space-y-3">
                    @foreach ([
                        ['¿Cuánto cuesta empezar?', 'Nada. El Perfil Digital es gratis, sin tarjeta de crédito y sin compromiso. Solo pagas los módulos que decidas activar.'],
                        ['¿Necesito saber de tecnología?', 'No. Todo se administra desde un panel simple: escribes, subes fotos y activas lo que quieras. Si sabes usar WhatsApp, puedes usar SomosSimple.'],
                        ['¿Tengo que contratar todos los módulos?', 'No. Cada negocio elige lo que necesita. Empiezas con lo esencial y agregas soluciones a medida que aparecen nuevas necesidades.'],
                        ['¿Cómo acceden mis clientes?', 'Desde un enlace, un código QR o una etiqueta NFC. No necesitan instalar ninguna aplicación.'],
                        ['¿Para qué tipo de negocio sirve?', 'Restaurantes, cafeterías, food trucks, barberías, peluquerías, tiendas, emprendimientos, profesionales, servicios técnicos y más. Si necesitas que te encuentren y te contacten, te sirve.'],
                        ['¿Qué pasa si cambio mis precios o mi menú?', 'Nada. Tus accesos apuntan siempre a tu información actualizada. No reimprimes ni repagas.'],
                    ] as $faq)
                        <details class="card group p-5">
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <span class="font-semibold">{{ $faq[0] }}</span>
                                <span class="text-ink-faint transition-transform group-open:rotate-45 text-xl leading-none">+</span>
                            </summary>
                            <p class="mt-3 text-ink-soft text-[15px]">{{ $faq[1] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA FINAL --}}
        <section class="py-16 bg-app">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Hazlo simple. Haz crecer tu negocio.</h2>
                <p class="mt-3 text-lg text-ink-soft">Pequeñas necesidades. Soluciones simples. Un negocio más fácil de gestionar.</p>
                <div class="mt-7 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}" class="btn-primary h-13 px-8 text-base">Comienza gratis con tu Perfil Digital</a>
                    <a href="#soluciones" class="btn-secondary h-13 px-8 text-base">Ver soluciones</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-ink text-white/70">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 py-12 grid sm:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-ink text-sm font-bold">S</span>
                    <span class="text-white font-bold">SomosSimple<span class="text-primary">.cl</span></span>
                </div>
                <p class="mt-3 text-sm max-w-xs">Soluciones simples para las necesidades reales de tu negocio.</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-white/40">Producto</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="#como" class="hover:text-white">Cómo funciona</a></li>
                    <li><a href="#soluciones" class="hover:text-white">Soluciones</a></li>
                    <li><a href="#perfil" class="hover:text-white">Perfil Digital</a></li>
                    <li><a href="#qrnfc" class="hover:text-white">QR y NFC</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-white/40">Cuenta</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('register') }}" class="hover:text-white">Crear mi Perfil Digital gratis</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white">Iniciar sesión</a></li>
                    @auth
                        <li>
                            <a href="{{ route('logout') }}" class="hover:text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 py-5 text-xs">
                &copy; {{ date('Y') }} SomosSimple.cl. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>
