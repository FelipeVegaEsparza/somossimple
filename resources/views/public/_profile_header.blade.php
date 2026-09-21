@php($layout = $business->profileTheme()->layout())
@php($initial = \Illuminate\Support\Str::substr($business->name, 0, 1))

@if ($layout === 'overlay')
    {{-- Portada oscura con el nombre sobre la imagen --}}
    <div class="relative">
        @if ($business->cover_path)
            <img src="{{ asset('storage/'.$business->cover_path) }}" alt="Portada de {{ $business->name }}" class="w-full h-60 object-cover">
        @else
            <div class="w-full h-60" style="background: linear-gradient(160deg, var(--color-primary), var(--color-strong))"></div>
        @endif
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,.82), rgba(0,0,0,.2) 55%, rgba(0,0,0,.3))"></div>

        <button type="button" @click="shareOpen = true"
                class="absolute top-4 right-4 inline-flex items-center gap-2 rounded-full bg-surface/90 backdrop-blur px-3.5 h-9 text-xs font-semibold text-ink shadow-soft">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
            Compartir
        </button>

        <div class="absolute inset-x-5 bottom-5 flex items-center gap-3">
            @if ($business->logo_path)
                <img src="{{ asset('storage/'.$business->logo_path) }}" alt="Logo de {{ $business->name }}" class="w-16 h-16 object-cover rounded-full border-2 border-white/80 bg-white shadow-card shrink-0">
            @else
                <span class="inline-flex items-center justify-center w-16 h-16 rounded-full border-2 border-white/80 text-white font-bold text-2xl shadow-card shrink-0" style="background: var(--color-primary)">{{ $initial }}</span>
            @endif
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold tracking-tight text-white truncate">{{ $business->name }}</h1>
                @if ($openNow !== null)
                    <p class="mt-0.5 text-xs font-semibold {{ $openNow ? 'text-green-300' : 'text-white/60' }}">{{ $openNow ? 'Abierto ahora' : 'Cerrado ahora' }}</p>
                @endif
            </div>
        </div>
    </div>
@elseif ($layout === 'central')
    {{-- Sin portada: logo y nombre centrados --}}
    <div class="relative px-5 pt-10 text-center">
        <button type="button" @click="shareOpen = true"
                class="absolute top-4 right-4 inline-flex items-center gap-2 rounded-full bg-surface/90 backdrop-blur px-3.5 h-9 text-xs font-semibold text-ink shadow-soft">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
            Compartir
        </button>

        @if ($business->logo_path)
            <img src="{{ asset('storage/'.$business->logo_path) }}" alt="Logo de {{ $business->name }}" class="w-24 h-24 mx-auto object-cover rounded-full border-4 border-surface shadow-card">
        @else
            <span class="inline-flex items-center justify-center w-24 h-24 mx-auto rounded-full bg-strong text-white font-bold text-3xl shadow-card">{{ $initial }}</span>
        @endif
        <h1 class="mt-4 text-3xl font-extrabold tracking-tight">{{ $business->name }}</h1>
        @if ($openNow !== null)
            <span class="badge mt-3 {{ $openNow ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $openNow ? 'Abierto ahora' : 'Cerrado ahora' }}</span>
        @endif
    </div>
@elseif ($layout === 'hero')
    {{-- Bloque de color con logo y nombre --}}
    <div class="relative px-5 pt-6">
        <div class="rounded-2xl px-6 py-8 text-center text-white shadow-card" style="background: linear-gradient(155deg, var(--color-primary), var(--color-primary-deep))">
            <button type="button" @click="shareOpen = true"
                    class="absolute top-9 right-9 inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur px-3 h-8 text-xs font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
                Compartir
            </button>

            @if ($business->logo_path)
                <img src="{{ asset('storage/'.$business->logo_path) }}" alt="Logo de {{ $business->name }}" class="w-20 h-20 mx-auto object-cover rounded-2xl border-4 border-white/30 bg-white shadow-card">
            @else
                <span class="inline-flex items-center justify-center w-20 h-20 mx-auto rounded-2xl border-4 border-white/30 bg-white/15 text-white font-bold text-2xl">{{ $initial }}</span>
            @endif
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight">{{ $business->name }}</h1>
            @if ($openNow !== null)
                <p class="mt-1 text-xs font-semibold text-white/80">{{ $openNow ? 'Abierto ahora' : 'Cerrado ahora' }}</p>
            @endif
        </div>
    </div>
@else
    {{-- Portada con logo superpuesto (clásico) --}}
    <div class="relative">
        @if ($business->cover_path)
            <img src="{{ asset('storage/'.$business->cover_path) }}" alt="Portada de {{ $business->name }}" class="w-full h-44 object-cover">
        @else
            <div class="w-full h-44 bg-strong"></div>
        @endif
        <button type="button" @click="shareOpen = true"
                class="absolute top-4 right-4 inline-flex items-center gap-2 rounded-full bg-surface/90 backdrop-blur px-3.5 h-9 text-xs font-semibold text-ink shadow-soft">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
            Compartir
        </button>
    </div>

    <div class="px-5">
        <div class="relative z-10 -mt-10 flex items-end justify-between gap-3">
            @if ($business->logo_path)
                <img src="{{ asset('storage/'.$business->logo_path) }}" alt="Logo de {{ $business->name }}" class="relative z-10 w-20 h-20 object-cover rounded-2xl border-4 border-surface bg-surface shadow-card">
            @else
                <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl border-4 border-surface bg-app text-ink font-bold text-2xl shadow-card">{{ $initial }}</span>
            @endif

            @if ($openNow !== null)
                <span class="badge mb-1 {{ $openNow ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">
                    {{ $openNow ? 'Abierto ahora' : 'Cerrado ahora' }}
                </span>
            @endif
        </div>

        <h1 class="mt-4 text-3xl font-extrabold tracking-tight">{{ $business->name }}</h1>
    </div>
@endif
