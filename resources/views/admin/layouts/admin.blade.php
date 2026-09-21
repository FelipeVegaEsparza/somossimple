<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administración') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="min-h-screen flex">
        <aside class="hidden lg:flex w-60 shrink-0 flex-col border-r border-line bg-ink text-white">
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2 px-5 h-16 border-b border-white/10">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary text-white text-sm font-bold">S</span>
                <span class="text-[15px] font-bold tracking-tight">Administración</span>
            </a>
            <nav class="flex-1 px-3 py-4 space-y-1">
                <p class="px-3 pt-4 pb-1.5 text-[11px] font-bold uppercase tracking-widest text-white/40 first:pt-0">General</p>
                <a href="{{ route('admin.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.index') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.business.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.business.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v2.25M18.75 3v2.25M6 5.25h12l.75 15.75h-13.5L6 5.25ZM9 9h2.25m-2.25 3.75h4.5"/></svg>
                    Negocios
                </a>
                <p class="px-3 pt-4 pb-1.5 text-[11px] font-bold uppercase tracking-widest text-white/40">Comercial</p>
                <a href="{{ route('admin.billing.prices.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.billing.prices.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                    Precios
                </a>
                <a href="{{ route('admin.billing.payments.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.billing.payments.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Pagos
                </a>
                <p class="px-3 pt-4 pb-1.5 text-[11px] font-bold uppercase tracking-widest text-white/40">Producto físico</p>
                <a href="{{ route('admin.kits.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.kits.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                    Kits
                </a>
                <a href="{{ route('admin.kitrequests.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.kitrequests.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    Solicitudes
                </a>
                <p class="px-3 pt-4 pb-1.5 text-[11px] font-bold uppercase tracking-widest text-white/40">Operación</p>
                <a href="{{ route('admin.modulerequests.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ request()->routeIs('admin.modulerequests.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Activaciones
                </a>
            </nav>
            <div class="border-t border-white/10 p-4 space-y-2">
                @if (auth()->user()->business)
                    <a href="{{ route('panel.index') }}" class="block text-xs font-semibold text-white/70 hover:text-white">Ir a mi panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-white/70 hover:text-white">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden flex items-center justify-between px-4 h-14 border-b border-line bg-surface">
                <a href="{{ route('admin.index') }}" class="font-bold">Administración</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-ink-soft">Salir</button>
                </form>
            </header>
            <main class="flex-1 px-4 sm:px-8 py-6 sm:py-8">
                <div class="mx-auto max-w-5xl">
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
