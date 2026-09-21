@extends('layouts.panel')

@section('title', 'Perfil')

@section('content')
    <div class="flex items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Perfil público</h1>
            <p class="text-sm text-ink-soft mt-1">Esta información se muestra a quien escanea tu QR o abre tu enlace.</p>
        </div>
        <p class="text-sm text-ink-soft">Tu página: <a class="font-mono text-primary hover:underline" href="{{ route('p.show', $business->slug) }}">/{{ $business->slug }}</a></p>
    </div>

    <form method="POST" action="{{ route('panel.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Información</h2>

            <div class="mt-5 grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="label">Nombre comercial</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $business->name) }}" required class="input @error('name') input-error @enderror">
                    @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="label">Descripción</label>
                    <textarea id="description" name="description" rows="3" class="input h-auto py-3">{{ old('description', $business->description) }}</textarea>
                </div>

                <div>
                    <label for="logo" class="label">Logo</label>
                    <input id="logo" type="file" name="logo" accept="image/*" class="input">
                    @error('logo') <p class="error-msg">{{ $message }}</p> @enderror
                    @if ($business->logo_path)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ asset('storage/'.$business->logo_path) }}" alt="Logo actual" class="w-16 h-16 object-cover rounded-lg border border-line bg-app">
                            <button type="submit" form="delete-logo" class="btn-ghost text-danger" onclick="return confirm('¿Eliminar el logo?')">Eliminar logo</button>
                        </div>
                    @endif
                </div>

                <div>
                    <label for="cover" class="label">Imagen de portada</label>
                    <input id="cover" type="file" name="cover" accept="image/*" class="input">
                    @error('cover') <p class="error-msg">{{ $message }}</p> @enderror
                    @if ($business->cover_path)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ asset('storage/'.$business->cover_path) }}" alt="Portada actual" class="w-28 h-16 object-cover rounded-lg border border-line bg-app">
                            <button type="submit" form="delete-cover" class="btn-ghost text-danger" onclick="return confirm('¿Eliminar la imagen de portada?')">Eliminar portada</button>
                        </div>
                    @endif
                </div>

                <div>
                    <label for="phone" class="label">Teléfono</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $business->phone) }}" class="input">
                </div>

                <div>
                    <label for="whatsapp" class="label">WhatsApp</label>
                    <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp', $business->whatsapp) }}" class="input" placeholder="+56 9 1234 5678">
                </div>

                <div>
                    <label for="contact_email" class="label">Correo de contacto</label>
                    <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $business->contact_email) }}" class="input">
                    @error('contact_email') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="website" class="label">Sitio web</label>
                    <input id="website" type="url" name="website" value="{{ old('website', $business->website) }}" class="input" placeholder="https://…">
                    @error('website') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="opening_hours" class="label">Horarios</label>
                    <input id="opening_hours" type="text" name="opening_hours" value="{{ old('opening_hours', $business->opening_hours) }}" class="input" placeholder="Lun a Sáb, 9:00 a 18:00">
                </div>

                <div>
                    <label for="address" class="label">Dirección</label>
                    <input id="address" type="text" name="address" value="{{ old('address', $business->address) }}" class="input">
                </div>

                <div class="sm:col-span-2">
                    <label for="map_url" class="label">Ubicación en el mapa (enlace de Google Maps)</label>
                    <input id="map_url" type="url" name="map_url" value="{{ old('map_url', $business->map_url) }}" class="input" placeholder="https://maps.google.com/…">
                    @error('map_url') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Redes sociales</h2>
            <p class="text-sm text-ink-soft mt-0.5">Deja vacío lo que no uses.</p>

            <div class="mt-5 grid sm:grid-cols-2 gap-4">
                @foreach ($networks as $network)
                    @php($link = $business->socialLinks->firstWhere('network', $network))
                    <div>
                        <label for="net-{{ $network }}" class="label">{{ ucfirst($network) }}</label>
                        <input id="net-{{ $network }}" type="url" name="networks[{{ $network }}]" value="{{ old("networks.$network", $link?->url) }}" class="input" placeholder="https://…">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Diseño del perfil</h2>
            <p class="text-sm text-ink-soft mt-0.5">Elige el estilo de tu página pública. Puedes cambiarlo cuando quieras.</p>

            <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ($themes as $theme)
                    @php($preview = $theme->preview())
                    <label class="cursor-pointer">
                        <input type="radio" name="theme" value="{{ $theme->value }}" @checked(old('theme', $business->theme ?: 'clasico') === $theme->value) class="sr-only peer">
                        <div class="rounded-xl border border-line p-3 transition peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20">
                            <div class="h-20 rounded-lg overflow-hidden border border-line" style="background: {{ $preview['app'] }}">
                                <div class="h-7" style="background: {{ $preview['ink'] }}"></div>
                                <div class="p-2">
                                    <div class="h-2 w-10 rounded-full" style="background: {{ $preview['primary'] }}"></div>
                                    <div class="mt-2 h-4 w-20 rounded" style="background: {{ $preview['surface'] }}"></div>
                                </div>
                            </div>
                            <p class="mt-2 text-sm font-semibold">{{ $theme->label() }}</p>
                            <p class="text-xs text-ink-soft">{{ $theme->description() }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="card p-6" x-data="{ buttons: @json($business->customButtons->map(fn ($b) => ['label' => $b->label, 'url' => $b->url])) }">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Botones personalizados</h2>
                    <p class="text-sm text-ink-soft mt-0.5">Por ejemplo: "Solicitar presupuesto" o "Ver tienda online".</p>
                </div>
                <button type="button" class="btn-secondary" @click="buttons.push({label:'', url:''})">Agregar botón</button>
            </div>

            <div class="mt-5 space-y-3">
                <template x-for="(btn, i) in buttons" :key="i">
                    <div class="grid grid-cols-[1fr_2fr_auto] gap-3 items-start">
                        <input type="text" x-model="btn.label" :name="`buttons_label[${i}]`" placeholder="Etiqueta" class="input">
                        <input type="url" x-model="btn.url" :name="`buttons_url[${i}]`" placeholder="https://…" class="input">
                        <button type="button" class="btn-ghost text-danger" @click="buttons.splice(i, 1)" aria-label="Quitar botón">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
                <p x-show="buttons.length === 0" class="text-sm text-ink-faint">Sin botones personalizados todavía.</p>
            </div>
        </div>

        @if ($business->gallery->isNotEmpty())
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Galería de imágenes</h2>
                <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ($business->gallery as $image)
                        <figure class="relative">
                            <img src="{{ asset('storage/'.$image->path) }}" alt="Imagen del perfil" class="w-full h-28 object-cover rounded-lg border border-line">
                            <button type="submit" form="delete-gallery-{{ $image->id }}" class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-7 h-7 rounded-md bg-surface border border-line text-danger shadow-soft" aria-label="Eliminar imagen">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Agregar imágenes</h2>
            <label for="gallery" class="label mt-5">Galería (puedes elegir varias)</label>
            <input id="gallery" type="file" name="gallery[]" multiple accept="image/*" class="input">
            @error('gallery') <p class="error-msg">{{ $message }}</p> @enderror

            <div class="mt-5 flex justify-end">
                <button type="submit" class="btn-primary">Guardar perfil</button>
            </div>
        </div>
    </form>

    {{-- Formularios de eliminación fuera del formulario principal (no se pueden anidar). --}}
    <form id="delete-logo" method="POST" action="{{ route('panel.profile.logo.destroy') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <form id="delete-cover" method="POST" action="{{ route('panel.profile.cover.destroy') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @foreach ($business->gallery as $image)
        <form id="delete-gallery-{{ $image->id }}" method="POST" action="{{ route('panel.profile.gallery.destroy', $image) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
