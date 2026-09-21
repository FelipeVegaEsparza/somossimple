@extends('layouts.panel')

@section('title', $event ? 'Editar evento' : 'Crear evento')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-ink-soft">Ticketera · Eventos</p>
            <h1 class="text-3xl font-bold tracking-tight">{{ $event ? 'Editar evento' : 'Crear evento' }}</h1>
        </div>
        <div class="flex gap-2">
            @if ($event)
                <a href="{{ route('panel.ticketera.events.show', $event) }}" class="btn-secondary">Estadísticas</a>
            @endif
            <a href="{{ route('panel.ticketera.events.index') }}" class="btn-ghost">← Volver</a>
        </div>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    @if ($event && $event->isPublished())
        <div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-line bg-app px-4 py-3 text-sm">
            <span class="font-semibold">Enlace público:</span>
            <a href="{{ route('ticketera.event', $event->slug) }}" target="_blank" rel="noopener" class="font-mono text-primary">{{ route('ticketera.event', $event->slug) }}</a>
        </div>
    @endif

    <form method="POST" action="{{ $event ? route('panel.ticketera.events.update', $event) : route('panel.ticketera.events.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if ($event)
            @method('PUT')
        @endif

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Información básica</h2>
            <div class="mt-5 space-y-4">
                <div>
                    <label for="name" class="label">Nombre del evento</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $event?->name) }}" required class="input @error('name') input-error @enderror" placeholder="Fiesta Patagonia 2026">
                    @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="label">Descripción</label>
                    <textarea id="description" name="description" rows="4" class="input h-auto py-3">{{ old('description', $event?->description) }}</textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="label">Categoría</label>
                        <input id="category" type="text" name="category" value="{{ old('category', $event?->category) }}" class="input" placeholder="Música, deporte, taller…">
                    </div>
                    <div>
                        <label for="organizer" class="label">Organizador</label>
                        <input id="organizer" type="text" name="organizer" value="{{ old('organizer', $event?->organizer) }}" class="input">
                    </div>
                    <div>
                        <label for="contact_phone" class="label">Teléfono de contacto</label>
                        <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone', $event?->contact_phone) }}" class="input">
                    </div>
                    <div>
                        <label for="contact_email" class="label">Correo de contacto</label>
                        <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $event?->contact_email) }}" class="input">
                    </div>
                </div>
                <div>
                    <label for="image" class="label">Imagen principal</label>
                    <input id="image" type="file" name="image" accept="image/*" class="input">
                    @if ($event?->image_path)
                        <img src="{{ asset('storage/'.$event->image_path) }}" alt="" class="mt-3 w-40 h-24 object-cover rounded-lg border border-line">
                    @endif
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Fecha</h2>
            <div class="mt-5 grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="starts_at" class="label">Inicio</label>
                    <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d\TH:i')) }}" required class="input @error('starts_at') input-error @enderror">
                    @error('starts_at') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ends_at" class="label">Término (opcional)</label>
                    <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $event?->ends_at?->format('Y-m-d\TH:i')) }}" class="input @error('ends_at') input-error @enderror">
                    @error('ends_at') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Lugar</h2>
            <div class="mt-5 grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="venue_name" class="label">Nombre del lugar</label>
                    <input id="venue_name" type="text" name="venue_name" value="{{ old('venue_name', $event?->venue_name) }}" class="input" placeholder="Gimnasio Municipal de Chile Chico">
                </div>
                <div>
                    <label for="venue_address" class="label">Dirección</label>
                    <input id="venue_address" type="text" name="venue_address" value="{{ old('venue_address', $event?->venue_address) }}" class="input">
                </div>
                <div>
                    <label for="venue_city" class="label">Ciudad</label>
                    <input id="venue_city" type="text" name="venue_city" value="{{ old('venue_city', $event?->venue_city) }}" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label for="venue_info" class="label">Información adicional</label>
                    <textarea id="venue_info" name="venue_info" rows="2" class="input h-auto py-3" placeholder="Acceso por calle lateral, estacionamiento, etc.">{{ old('venue_info', $event?->venue_info) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Publicación</h2>
            <div class="mt-5 grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="label">Estado</label>
                    <select id="status" name="status" class="input">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $event?->status()?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="commission_rate" class="label">Comisión de plataforma (%)</label>
                    <input id="commission_rate" type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ old('commission_rate', $event?->commission_rate ?? 0) }}" class="input">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">{{ $event ? 'Guardar evento' : 'Crear evento' }}</button>
        </div>
    </form>

    @if ($event)
        <div class="card p-6 mt-4">
            <h2 class="text-lg font-semibold">Tipos de entrada</h2>
            <p class="text-sm text-ink-soft mt-0.5">Define precios y cupos. El stock se descuenta automáticamente al vender.</p>

            @if ($event->ticketTypes->isNotEmpty())
                <div class="mt-4 space-y-2">
                    @foreach ($event->ticketTypes as $type)
                        <details class="rounded-xl border border-line p-4">
                            <summary class="flex cursor-pointer items-center justify-between gap-3 list-none">
                                <div>
                                    <p class="font-semibold">{{ $type->name }} <span class="text-ink-soft font-normal">· {{ $type->priceDisplay() }}</span></p>
                                    <p class="text-xs text-ink-soft">Vendidas {{ $type->sold }} / {{ $type->stock }} · Disponibles {{ $type->remaining() }} @if ($type->isSoldOut()) · <span class="text-danger font-semibold">Agotado</span> @endif</p>
                                </div>
                                <span class="badge {{ $type->is_active ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $type->is_active ? 'Activo' : 'Inactivo' }}</span>
                            </summary>

                            <form method="POST" action="{{ route('panel.ticketera.types.update', [$event, $type]) }}" class="mt-4 grid sm:grid-cols-2 gap-3">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $type->name }}" class="input" required>
                                <input type="number" name="price" value="{{ $type->price }}" min="0" class="input" required>
                                <input type="number" name="stock" value="{{ $type->stock }}" min="0" class="input" required>
                                <input type="number" name="purchase_limit" value="{{ $type->purchase_limit }}" min="1" class="input" placeholder="Límite por persona (opcional)">
                                <input type="datetime-local" name="sales_start_at" value="{{ $type->sales_start_at?->format('Y-m-d\TH:i') }}" class="input">
                                <input type="datetime-local" name="sales_end_at" value="{{ $type->sales_end_at?->format('Y-m-d\TH:i') }}" class="input">
                                <textarea name="description" rows="2" class="input h-auto py-2 sm:col-span-2" placeholder="Descripción (opcional)">{{ $type->description }}</textarea>
                                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($type->is_active) class="w-4 h-4 rounded border-line text-primary"> Activo</label>
                                <div class="flex justify-end gap-2 sm:col-span-2">
                                    <button type="submit" class="btn-secondary">Guardar cambios</button>
                                </div>
                            </form>

                            <div class="mt-2 flex justify-end gap-2">
                                <form method="POST" action="{{ route('panel.ticketera.types.toggle', [$event, $type]) }}">
                                    @csrf
                                    <button type="submit" class="btn-ghost text-ink-soft">{{ $type->is_active ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                                <form method="POST" action="{{ route('panel.ticketera.types.destroy', [$event, $type]) }}" onsubmit="return confirm('¿Eliminar este tipo de entrada?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-danger">Eliminar</button>
                                </form>
                            </div>
                        </details>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-ink-soft">Aún no hay tipos de entrada.</p>
            @endif

            <form method="POST" action="{{ route('panel.ticketera.types.store', $event) }}" class="mt-5 border-t border-line pt-5 grid sm:grid-cols-2 gap-3">
                @csrf
                <div class="sm:col-span-2">
                    <label class="label">Nuevo tipo de entrada</label>
                </div>
                <input type="text" name="name" class="input" placeholder="General, Preventa, VIP…" required>
                <input type="number" name="price" class="input" placeholder="Precio (CLP)" min="0" required>
                <input type="number" name="stock" class="input" placeholder="Cantidad disponible" min="0" required>
                <input type="number" name="purchase_limit" class="input" placeholder="Límite por persona (opcional)" min="1">
                <input type="datetime-local" name="sales_start_at" class="input">
                <input type="datetime-local" name="sales_end_at" class="input">
                <textarea name="description" rows="2" class="input h-auto py-2 sm:col-span-2" placeholder="Descripción (opcional)"></textarea>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-line text-primary"> Activo</label>
                <div class="flex justify-end sm:col-span-2">
                    <button type="submit" class="btn-secondary">Agregar tipo de entrada</button>
                </div>
            </form>
        </div>
    @endif
@endsection
