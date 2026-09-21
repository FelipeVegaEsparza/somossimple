<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="min-h-screen flex">
        <aside class="hidden lg:flex w-60 shrink-0 flex-col border-r border-line bg-surface">
            <a href="{{ route('panel.index') }}" class="flex items-center gap-2 px-5 h-16 border-b border-line">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-ink text-white text-sm font-bold">S</span>
                <span class="text-[15px] font-bold tracking-tight">SomosSimple<span class="text-primary">.cl</span></span>
            </a>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @foreach (App\Http\Navigation::sections() as $section)
                    <p class="px-3 pt-4 pb-1.5 text-[11px] font-bold uppercase tracking-widest text-ink-faint first:pt-0">{{ $section['label'] }}</p>
                    @foreach ($section['items'] as $item)
                        @php($active = request()->routeIs($item['active']))
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium {{ $active ? 'bg-primary-tint text-primary' : 'text-ink-soft hover:bg-app hover:text-ink' }}">
                            <span class="text-base leading-none">{!! $item['icon'] !!}</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                @endforeach

                @if (auth()->user()->is_platform_admin && ! session()->has('admin_original_user_id'))
                    <p class="px-3 pt-4 pb-1.5 text-[11px] font-bold uppercase tracking-widest text-ink-faint">Plataforma</p>
                    <a href="{{ route('admin.index') }}" class="flex items-center gap-3 px-3 h-10 rounded-lg text-sm font-medium text-ink-soft hover:bg-app hover:text-ink">
                        <span class="text-base leading-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                        </span>
                        <span>Administración</span>
                    </a>
                @endif
            </nav>
            <div class="border-t border-line p-4">
                <p class="text-xs text-ink-faint mb-1">@auth {{ auth()->user()->business?->name ?? 'Sin negocio' }} @endauth</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-ink-soft hover:text-danger">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden flex items-center justify-between px-4 h-14 border-b border-line bg-surface">
                <a href="{{ route('panel.index') }}" class="flex items-center gap-2 font-bold">SomosSimple<span class="text-primary">.cl</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-ink-soft">Salir</button>
                </form>
            </header>
            <main class="flex-1 px-4 sm:px-8 py-6 sm:py-8">
                <div class="mx-auto max-w-5xl">
                    @if (session()->has('admin_original_user_id'))
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-primary/30 bg-primary-tint px-4 py-3 text-sm text-ink">
                            <span class="font-semibold">Estás actuando como {{ auth()->user()->business?->name ?? 'este usuario' }} (personificación)</span>
                            <form method="POST" action="{{ route('admin.impersonate.leave') }}">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-primary hover:underline">Volver al panel de administración</button>
                            </form>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink shadow-soft">{{ session('status') }}</div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
