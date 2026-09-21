@extends('layouts.panel')

@section('title', 'Configuración de reservas')

@section('content')
    <div class="flex items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Configurar reservas</h1>
            <p class="text-sm text-ink-soft mt-1">Servicios reservables, horarios de atención y días no disponibles.</p>
        </div>
        <a href="{{ route('panel.reservations.index') }}" class="btn-ghost">← Volver a la agenda</a>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Servicios reservables</h2>
                <p class="text-sm text-ink-soft mt-0.5">Los servicios de reservas son propios del módulo, independientes del catálogo.</p>

                <form method="POST" action="{{ route('panel.reservations.service.store') }}" class="mt-5 grid grid-cols-2 gap-3">
                    @csrf
                    <div class="col-span-2">
                        <label for="name" class="label">Nombre del servicio</label>
                        <input id="name" type="text" name="name" class="input" placeholder="Corte clásico" required>
                    </div>
                    <div>
                        <label for="duration_minutes" class="label">Duración (min)</label>
                        <input id="duration_minutes" type="number" name="duration_minutes" class="input" value="30" min="5" required>
                    </div>
                    <div>
                        <label for="price" class="label">Precio CLP (opcional)</label>
                        <input id="price" type="number" name="price" class="input" min="0" placeholder="12000">
                    </div>
                    <button type="submit" class="btn-secondary col-span-2">Agregar servicio</button>
                </form>

                @if ($business->bookingServices->isNotEmpty())
                    <ul class="mt-5 divide-y divide-line">
                        @foreach ($business->bookingServices as $service)
                            <li class="py-3 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $service->name }} @if (! $service->active)<span class="badge bg-app text-ink-soft ml-2">Inactivo</span>@endif</p>
                                    <p class="text-xs text-ink-soft">{{ $service->duration_minutes }} min{{ $service->priceDisplay() ? ' · '.$service->priceDisplay() : '' }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('panel.reservations.service.toggle', $service) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-primary">{{ $service->active ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('panel.reservations.service.destroy', $service) }}" onsubmit="return confirm('¿Eliminar este servicio? Las reservas pasadas se conservan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-danger">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Días no disponibles</h2>
                <form method="POST" action="{{ route('panel.reservations.dayoff.store') }}" class="mt-4 flex gap-3">
                    @csrf
                    <input type="date" name="date" class="input" required>
                    <button type="submit" class="btn-secondary shrink-0">Bloquear día</button>
                </form>

                @if ($dayOffs->isNotEmpty())
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($dayOffs as $dayOff)
                            <li class="py-2.5 flex items-center justify-between">
                                <span class="text-sm">{{ $dayOff->date->translatedFormat('l d F Y') }}</span>
                                <form method="POST" action="{{ route('panel.reservations.dayoff.destroy', $dayOff) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-danger">Quitar</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="card p-6 self-start">
            <h2 class="text-lg font-semibold">Horarios de atención</h2>
            <p class="text-sm text-ink-soft mt-0.5">Deja vacío el día que no atiendes.</p>

            <form method="POST" action="{{ route('panel.reservations.hours') }}" class="mt-5 space-y-3">
                @csrf
                @for ($day = 0; $day < 7; $day++)
                    @php($range = $weekHours->get($day))
                    <div class="grid grid-cols-[5rem_1fr_1fr] gap-3 items-center">
                        <span class="text-sm font-medium">{{ $dayNames[$day] }}</span>
                        <input type="time" name="days[{{ $day }}][open]" value="{{ $range?->open_time ? substr($range->open_time, 0, 5) : '' }}" class="input">
                        <input type="time" name="days[{{ $day }}][close]" value="{{ $range?->close_time ? substr($range->close_time, 0, 5) : '' }}" class="input">
                    </div>
                @endfor

                <button type="submit" class="btn-primary w-full mt-2">Guardar horarios</button>
            </form>
        </div>
    </div>
@endsection
