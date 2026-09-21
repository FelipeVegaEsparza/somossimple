<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $business->name }}</title>
    <meta name="description" content="{{ Str::limit($business->description, 160) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('public._pwa', ['business' => $business])
</head>
<body class="bg-app theme-{{ $business->theme ?: 'clasico' }}">
    @php($url = route('p.show', $business->slug, true))

    @php($contactoTexto = trim($business->name.' · '.($business->whatsapp ?: $business->phone ?: '').' · '.$url, ' ·'))
    @php($layout = $business->profileTheme()->layout())
    <div class="max-w-md mx-auto min-h-screen bg-surface pb-28 shadow-card" data-layout="{{ $layout }}"
         x-data="{
            shareOpen: false,
            installOpen: false,
            deferredPrompt: null,
            canInstall: false,
            isIos: /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
            standalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
            init() {
                window.addEventListener('beforeinstallprompt', (event) => {
                    event.preventDefault();
                    this.deferredPrompt = event;
                    this.canInstall = true;
                });
                window.addEventListener('appinstalled', () => {
                    this.deferredPrompt = null;
                    this.canInstall = false;
                    this.installOpen = false;
                });
            },
            async install() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    await this.deferredPrompt.userChoice;
                    this.deferredPrompt = null;
                    this.canInstall = false;
                    return;
                }
                this.installOpen = true;
            },
            copied: null,
            url: @js($url),
            contacto: @js($contactoTexto),
            copy(text, key) {
                navigator.clipboard.writeText(text);
                this.copied = key;
                setTimeout(() => this.copied = null, 1800);
            },
            shareWhatsApp() {
                window.open('https://wa.me/?text=' + encodeURIComponent(@js($business->name).concat(': ').concat(this.url)), '_blank');
            },
            shareFacebook() {
                window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(this.url), '_blank');
            },
            shareInstagram() {
                navigator.clipboard.writeText(this.url);
                this.copied = 'instagram';
                window.open('https://instagram.com', '_blank');
                setTimeout(() => this.copied = null, 2500);
            }
         }"
         @keydown.escape.window="shareOpen = false; installOpen = false">
        @include('public._profile_header', ['business' => $business, 'openNow' => $openNow])

        <div class="px-5 {{ $layout === 'cover' ? '' : 'pt-4' }}">
            @if ($business->description)
                <p class="mt-2 text-[15px] leading-relaxed text-ink-soft">{{ $business->description }}</p>
            @endif

            @if ($business->opening_hours)
                <p class="mt-3 flex items-center gap-2 text-sm text-ink-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5 shrink-0 text-ink-faint"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ $business->opening_hours }}
                </p>
            @endif

            {{-- Acciones principales --}}
            <div class="mt-6 grid grid-cols-2 gap-2.5">
                @if ($business->whatsapp)
                    @php($wa = preg_replace('/[^0-9]/', '', $business->whatsapp))
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       data-track-url="{{ route('p.click', [$business->slug, 'whatsapp']) }}"
                       class="flex items-center justify-center gap-2 h-12 rounded-xl text-white font-semibold text-sm" style="background-color:#25D366">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2Zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.26 8.26 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.54-3.7 8.24-8.24 8.24Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29Z"/></svg>
                        WhatsApp
                    </a>
                @endif

                @if ($business->phone)
                    <a href="tel:{{ $business->phone }}" data-track-url="{{ route('p.click', [$business->slug, 'llamar']) }}"
                       class="flex items-center justify-center gap-2 h-12 rounded-xl bg-strong text-white font-semibold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        Llamar
                    </a>
                @endif

                @if ($business->map_url)
                    <a href="{{ $business->map_url }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 h-12 rounded-xl bg-surface border border-line text-ink font-semibold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        Ubicación
                    </a>
                @endif
            </div>

            {{-- Enlaces secundarios --}}
            @if ($business->contact_email || $business->website || $business->customButtons->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm">
                    @if ($business->contact_email)
                        <a href="mailto:{{ $business->contact_email }}" class="font-semibold text-ink-soft hover:text-ink">Correo</a>
                    @endif
                    @if ($business->website)
                        <a href="{{ $business->website }}" target="_blank" rel="noopener" class="font-semibold text-ink-soft hover:text-ink">Sitio web</a>
                    @endif
                    @foreach ($business->customButtons as $button)
                        <a href="{{ $button->url }}" target="_blank" rel="noopener" class="font-semibold text-primary hover:underline">{{ $button->label }}</a>
                    @endforeach
                </div>
            @endif

            @if ($business->address)
                <p class="mt-3 flex items-start gap-2 text-sm text-ink-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5 shrink-0 text-ink-faint"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    {{ $business->address }}
                </p>
            @endif

            {{-- Instalar como aplicación (PWA) --}}
            <button type="button" x-cloak x-show="!standalone" x-on:click="install()"
                    class="mt-4 flex w-full items-center justify-center gap-2 h-12 rounded-xl border border-line bg-surface text-sm font-semibold text-ink transition-colors hover:border-primary/40">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Instalar APP
            </button>

            {{-- Acceso a los módulos activos: cada card abre su página en una pestaña nueva --}}
            @if (! empty($accesos))
                <div class="mt-8 border-t border-line pt-6">
                    <h2 class="text-base font-bold tracking-tight text-ink">Explora</h2>
                    <div class="mt-4 grid grid-cols-2 gap-2.5">
                        @foreach ($accesos as $acceso)
                            <a href="{{ route($acceso['route'], $business->slug) }}" target="_blank" rel="noopener"
                               class="group flex items-center gap-2.5 rounded-xl border border-line px-3.5 py-3 transition-colors hover:border-primary/40">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-strong text-white shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $acceso['icon'] }}"/>
                                    </svg>
                                </span>
                                <span class="flex-1 min-w-0 text-sm font-semibold leading-tight break-words">{{ $acceso['label'] }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0 text-ink-faint transition-colors group-hover:text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-4.5-6H21m0 0v7.5m0-7.5L10.5 13.5"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Redes --}}
            @if ($business->socialLinks->isNotEmpty())
                <x-module-section :icon="$iconos['social']" title="Síguenos">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($business->socialLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener"
                               @if($link->network === 'instagram') data-track-url="{{ route('p.click', [$business->slug, 'instagram']) }}" @endif
                               class="inline-flex items-center gap-2 rounded-full border border-line px-4 h-10 text-sm font-semibold text-ink-soft hover:text-ink hover:border-ink-faint">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                {{ $link->displayLabel() }}
                            </a>
                        @endforeach
                    </div>
                </x-module-section>
            @endif

            <p class="mt-10 pb-4 text-center text-xs text-ink-faint">
                Perfil digital creado con <a href="{{ route('landing.home') }}" class="font-semibold text-ink-soft hover:text-ink">{{ config('app.name') }}</a>
            </p>
        </div>

        {{-- Modal compartir --}}
        <div x-cloak x-show="shareOpen" class="fixed inset-0 z-40 flex items-end sm:items-center justify-center px-0 sm:px-4"
             role="dialog" aria-modal="true" aria-label="Compartir perfil">
            <div class="absolute inset-0 bg-strong/40 backdrop-blur-sm" @click="shareOpen = false"></div>

            <div x-show="shareOpen" x-transition.opacity
                 class="relative w-full max-w-md bg-surface rounded-t-2xl sm:rounded-2xl shadow-card p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold">Compartir</h2>
                    <button type="button" @click="shareOpen = false" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-ink-soft hover:bg-app" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="text-sm text-ink-soft mt-1">Elige cómo compartir este perfil.</p>

                <div class="mt-4 space-y-2">
                    <button type="button" @click="shareWhatsApp()"
                            class="w-full flex items-center gap-3 rounded-xl border border-line px-4 h-12 text-sm font-semibold hover:border-ink-faint">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white" style="background-color:#25D366">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2Zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.26 8.26 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.54-3.7 8.24-8.24 8.24Z"/></svg>
                        </span>
                        WhatsApp
                    </button>

                    <button type="button" @click="shareFacebook()"
                            class="w-full flex items-center gap-3 rounded-xl border border-line px-4 h-12 text-sm font-semibold hover:border-ink-faint">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white" style="background-color:#1877F2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.52 1.5-3.91 3.78-3.91 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.9h2.78l-.45 2.9h-2.33V22c4.78-.79 8.44-4.94 8.44-9.94Z"/></svg>
                        </span>
                        Facebook
                    </button>

                    <button type="button" @click="shareInstagram()"
                            class="w-full flex items-center gap-3 rounded-xl border border-line px-4 h-12 text-sm font-semibold hover:border-ink-faint">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white" style="background:#E1306C">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                        </span>
                        <span class="flex-1 text-left">Instagram <span class="block text-xs font-normal text-ink-faint" x-text="copied === 'instagram' ? 'Enlace copiado: pégalo en tu historia' : 'Copia el enlace y pégalo en tu historia'"></span></span>
                    </button>

                    <button type="button" @click="copy(contacto, 'contacto')"
                            class="w-full flex items-center gap-3 rounded-xl border border-line px-4 h-12 text-sm font-semibold hover:border-ink-faint">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-tint text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/></svg>
                        </span>
                        <span class="flex-1 text-left" x-text="copied === 'contacto' ? '¡Contacto copiado!' : 'Copiar contacto'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal instalar APP --}}
        <div x-cloak x-show="installOpen" class="fixed inset-0 z-40 flex items-end sm:items-center justify-center px-0 sm:px-4"
             role="dialog" aria-modal="true" aria-label="Instalar aplicación">
            <div class="absolute inset-0 bg-strong/40 backdrop-blur-sm" @click="installOpen = false"></div>

            <div x-show="installOpen" x-transition.opacity
                 class="relative w-full max-w-md bg-surface rounded-t-2xl sm:rounded-2xl shadow-card p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold">Instalar APP</h2>
                    <button type="button" @click="installOpen = false" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-ink-soft hover:bg-app" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="mt-1 text-sm text-ink-soft">Agrega este perfil a tu pantalla de inicio para abrirlo como una app.</p>

                <div x-show="isIos" class="mt-4 text-sm">
                    <p class="font-semibold">En iPhone o iPad (Safari)</p>
                    <ol class="mt-2 list-decimal list-inside space-y-2 text-ink-soft">
                        <li>Toca el botón <strong class="text-ink">Compartir</strong> (el cuadrado con la flecha) en la barra inferior.</li>
                        <li>Desliza las opciones y elige <strong class="text-ink">Añadir a pantalla de inicio</strong>.</li>
                        <li>Toca <strong class="text-ink">Añadir</strong> para confirmar.</li>
                    </ol>
                </div>

                <div x-show="!isIos" class="mt-4 text-sm">
                    <p class="font-semibold">En tu navegador</p>
                    <ol class="mt-2 list-decimal list-inside space-y-2 text-ink-soft">
                        <li>Abre el <strong class="text-ink">menú</strong> del navegador (⋮ o ⋯).</li>
                        <li>Elige <strong class="text-ink">Instalar aplicación</strong> o <strong class="text-ink">Añadir a pantalla de inicio</strong>.</li>
                        <li>Confirma la instalación.</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Barra fija de conversión --}}
        @if ($business->whatsapp)
            <div class="fixed bottom-0 inset-x-0 z-20 border-t border-line bg-surface/95 backdrop-blur">
                <div class="max-w-md mx-auto px-4 py-3 flex gap-2.5">
                    @php($wa = preg_replace('/[^0-9]/', '', $business->whatsapp))
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       data-track-url="{{ route('p.click', [$business->slug, 'whatsapp']) }}"
                       class="flex-1 flex items-center justify-center gap-2 h-12 rounded-xl text-white font-semibold text-sm" style="background-color:#25D366">WhatsApp</a>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
