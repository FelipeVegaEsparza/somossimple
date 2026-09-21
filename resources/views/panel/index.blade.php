@extends('layouts.panel')

@section('title', 'Dashboard')

@section('content')
    @unless ($business)
        <div class="max-w-xl mx-auto">
            <div class="card p-8">
                <h1 class="text-2xl font-bold tracking-tight">Crea tu negocio</h1>
                <p class="mt-1 text-sm text-ink-soft">
                    Este es el primer paso: tu perfil digital, gratuito y siempre disponible.
                    Después podrás solicitar los módulos que necesites.
                </p>

                <form method="POST" action="{{ route('panel.business.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="label">Nombre de tu negocio</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="input @error('name') input-error @enderror"
                               placeholder="Por ejemplo: Barbería Patagonia">
                        @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">Crear negocio</button>
                </form>
            </div>
        </div>
    @else
        @php($url = route('p.show', $business->slug, true))

        <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-sm text-ink-soft">Hola, {{ explode(' ', auth()->user()->name)[0] }}</p>
                <h1 class="text-3xl font-bold tracking-tight">{{ $business->name }}</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge bg-[#e6f6ee] text-good">Perfil publicado</span>
                @if ($kitsActivos > 0)
                    <span class="badge bg-primary-tint text-primary">{{ $kitsActivos }} {{ $kitsActivos === 1 ? 'kit activo' : 'kits activos' }}</span>
                @endif
                @if ($activePaid->isNotEmpty())
                    <span class="badge bg-primary-tint text-primary">{{ $activePaid->count() }} {{ $activePaid->count() === 1 ? 'módulo' : 'módulos' }} activos</span>
                @endif
                @if ($atrasado)
                    <span class="badge bg-red-50 text-danger">Pago atrasado</span>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="flex flex-col lg:flex-row">
                <div class="flex-1 px-6 py-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-ink-faint">Tu página pública</p>
                    <a href="{{ route('p.show', $business->slug) }}" target="_blank" rel="noopener"
                       class="mt-1 block truncate font-mono text-lg font-semibold text-primary hover:underline">{{ $url }}</a>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button"
                                class="btn-secondary"
                                x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText(@js($url)); copied = true; setTimeout(() => copied = false, 1600)">
                            <span x-text="copied ? '¡Copiado!' : 'Copiar enlace'"></span>
                        </button>
                        <a href="{{ route('p.show', $business->slug) }}" target="_blank" rel="noopener" class="btn-secondary">Ver perfil</a>
                        <a href="https://wa.me/?text={{ urlencode('Mira mi negocio: '.$url) }}" target="_blank" rel="noopener" class="btn-secondary">Compartir</a>
                    </div>
                </div>

                <div class="hidden lg:block w-px bg-line"></div>

                <div class="flex divide-x divide-line">
                    <div class="flex-1 px-5 py-5 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tracking-tight leading-none">{{ number_format($stats['visits']) }}</p>
                            <p class="text-xs text-ink-soft mt-1">Visitas del perfil</p>
                        </div>
                    </div>
                    <div class="flex-1 px-5 py-5 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tracking-tight leading-none">{{ number_format($stats['qr']) }}</p>
                            <p class="text-xs text-ink-soft mt-1">Escaneos QR</p>
                        </div>
                    </div>
                    <div class="flex-1 px-5 py-5 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tracking-tight leading-none">{{ number_format($clients) }}</p>
                            <p class="text-xs text-ink-soft mt-1">Clientes</p>
                        </div>
                    </div>
                    <div class="flex-1 px-5 py-5 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-tint text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tracking-tight leading-none">{{ $proximas->count() }}</p>
                            <p class="text-xs text-ink-soft mt-1">Próximas reservas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-4">
            <div class="card p-6 flex flex-col">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Próximas reservas</h2>
                    @if ($business->isModuleActive(\App\Enums\Module::Reservations))
                        <a href="{{ route('panel.reservations.index') }}" class="text-sm font-semibold text-primary">Ver agenda</a>
                    @endif
                </div>

                @if ($proximas->isEmpty())
                    <div class="flex-1 flex flex-col items-center justify-center text-center rounded-xl bg-app px-6 py-8 mt-4">
                        <p class="font-semibold text-ink">Aún no tienes reservas próximas</p>
                        <p class="mt-1 text-sm text-ink-soft max-w-xs">Cuando tus clientes pidan hora, las verás aquí en orden.</p>
                        @if (! $business->isModuleActive(\App\Enums\Module::Reservations))
                            <a href="{{ route('panel.modules') }}" class="btn-secondary mt-4">Solicitar módulo de reservas</a>
                        @endif
                    </div>
                @else
                    <ul class="mt-4 divide-y divide-line flex-1">
                        @foreach ($proximas as $reservation)
                            <li class="py-3 flex items-center gap-4">
                                <div class="text-center w-14 shrink-0">
                                    <p class="text-lg font-extrabold leading-none">{{ $reservation->starts_at->format('H:i') }}</p>
                                    <p class="text-[11px] uppercase tracking-wide text-ink-faint mt-1">{{ $reservation->starts_at->format('d/m') }}</p>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold truncate">{{ $reservation->service_name }}</p>
                                    <p class="text-sm text-ink-soft truncate">{{ $reservation->client_name }} · {{ $reservation->phone }}</p>
                                </div>
                                <span class="badge {{ $reservation->status === 'confirmed' ? 'bg-primary-tint text-primary' : 'bg-[#fff6e0] text-warn' }}">
                                    {{ $reservation->status === 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="card p-6 flex flex-col">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Tus módulos</h2>
                    <a href="{{ route('panel.modules') }}" class="text-sm font-semibold text-primary">Gestionar</a>
                </div>
                @php($paidPct = $totalPaid > 0 ? round($activePaid->count() / $totalPaid * 100) : 0)
                <div class="mt-4 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full shrink-0"
                         style="background: conic-gradient(var(--color-primary) {{ $paidPct }}%, var(--color-line) {{ $paidPct }}% 100%);">
                        <div class="w-full h-full rounded-full grid place-items-center">
                            <span class="w-11 h-11 rounded-full bg-surface grid place-items-center text-sm font-bold">{{ $paidPct }}%</span>
                        </div>
                    </div>
                    <p class="text-sm text-ink-soft">{{ $activePaid->count() }} de {{ $totalPaid }} módulos de pago activos</p>
                </div>

                <ul class="mt-4 divide-y divide-line flex-1">
                    @foreach (App\Enums\Module::cases() as $module)
                        @php($access = $business->moduleAccess->firstWhere('module', $module->value))
                        <li class="py-3 flex items-center justify-between gap-3">
                            <span class="text-sm font-medium">{{ $module->label() }}</span>
                            @if ($module->isFree())
                                <span class="badge bg-primary-tint text-primary">Gratis</span>
                            @else
                                <span class="badge {{ $access?->active ? 'bg-[#e6f6ee] text-good' : 'bg-app text-ink-soft' }}">
                                    {{ $access?->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-4 mt-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Actividad de la semana</h2>
                <p class="text-xs text-ink-faint mt-0.5">Visitas a tu perfil</p>

                <div class="mt-4" x-data="{ range: '7' }">
                    <div class="flex items-center gap-1">
                        <button type="button" @click="range = '7'"
                                :class="range === '7' ? 'bg-primary-tint text-primary' : 'text-ink-soft hover:text-ink'"
                                class="rounded-lg px-3 h-8 text-xs font-semibold">7 días</button>
                        <button type="button" @click="range = '30'"
                                :class="range === '30' ? 'bg-primary-tint text-primary' : 'text-ink-soft hover:text-ink'"
                                class="rounded-lg px-3 h-8 text-xs font-semibold">30 días</button>
                    </div>

                    <div x-show="range === '7'" class="mt-4 flex items-end justify-between gap-2 h-24">
                        @forelse ($weekly as $day)
                            @php($h = max(6, $weekMax > 0 ? round($day['value'] / $weekMax * 84) : 6))
                            <div class="flex-1 flex flex-col items-center justify-end gap-1.5">
                                <div class="w-full rounded-t-md" style="height: {{ $h }}px; background: {{ $day['value'] > 0 ? 'var(--color-primary)' : 'var(--color-line)' }};"></div>
                                <span class="text-[11px] text-ink-faint">{{ $day['label'] }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-ink-faint">Sin visitas todavía.</p>
                        @endforelse
                    </div>

                    <div x-show="range === '30'" x-cloak class="mt-4 flex items-end gap-1 h-24">
                        @forelse ($monthly as $day)
                            @php($h = max(3, $monthMax > 0 ? round($day['value'] / $monthMax * 84) : 3))
                            <div class="flex-1 rounded-t-sm" style="height: {{ $h }}px; background: {{ $day['value'] > 0 ? 'var(--color-primary)' : 'var(--color-line)' }};" title="{{ $day['label'] }}: {{ $day['value'] }}"></div>
                        @empty
                            <p class="text-sm text-ink-faint">Sin visitas todavía.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-app p-4">
                        <p class="text-2xl font-bold">{{ number_format($stats['visits']) }}</p>
                        <p class="text-xs text-ink-soft">Visitas totales</p>
                    </div>
                    <div class="rounded-xl bg-app p-4">
                        <p class="text-2xl font-bold">{{ number_format($stats['qr']) }}</p>
                        <p class="text-xs text-ink-soft">Escaneos de kits</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Siguientes pasos</h2>
                <p class="text-xs text-ink-faint mt-0.5">Lo que puedes hacer hoy</p>

                @if ($activePaid->isNotEmpty())
                    <div class="mt-4 space-y-2.5">
                        @if ($activePaid->contains(fn ($m) => $m === \App\Enums\Module::Catalog))
                            <a href="{{ route('panel.catalog.create') }}" class="flex items-center justify-between rounded-xl border border-line bg-white px-4 py-3 text-sm font-semibold hover:border-primary/50"><span>Agregar un producto o servicio</span><span class="text-ink-faint">→</span></a>
                        @endif
                        @if ($activePaid->contains(fn ($m) => $m === \App\Enums\Module::Reservations))
                            <a href="{{ route('panel.reservations.config') }}" class="flex items-center justify-between rounded-xl border border-line bg-white px-4 py-3 text-sm font-semibold hover:border-primary/50"><span>Configurar horarios de reservas</span><span class="text-ink-faint">→</span></a>
                        @endif
                        @if ($activePaid->contains(fn ($m) => $m === \App\Enums\Module::Clients))
                            <a href="{{ route('panel.clients.create') }}" class="flex items-center justify-between rounded-xl border border-line bg-white px-4 py-3 text-sm font-semibold hover:border-primary/50"><span>Registrar tu primer cliente</span><span class="text-ink-faint">→</span></a>
                        @endif
                        @if ($activePaid->contains(fn ($m) => $m === \App\Enums\Module::Communications))
                            <a href="{{ route('panel.communications.create') }}" class="flex items-center justify-between rounded-xl border border-line bg-white px-4 py-3 text-sm font-semibold hover:border-primary/50"><span>Crear una comunicación</span><span class="text-ink-faint">→</span></a>
                        @endif
                    </div>
                @else
                    <div class="mt-4 rounded-xl bg-app px-5 py-6">
                        <p class="font-semibold text-ink">Potencia tu perfil</p>
                        <p class="mt-1 text-sm text-ink-soft">Solicita catálogo, reservas, clientes o comunicaciones cuando los necesites.</p>
                        <a href="{{ route('panel.modules') }}" class="btn-secondary mt-4">Ver módulos disponibles</a>
                    </div>
                @endif
            </div>
        </div>
    @endunless
@endsection
