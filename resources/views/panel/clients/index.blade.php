@extends('layouts.panel')

@section('title', 'Clientes')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Clientes</h1>
            <p class="text-sm text-ink-soft mt-1">Tu base de clientes. Cada negocio ve solo la suya.</p>
        </div>
        <a href="{{ route('panel.clients.create') }}" class="btn-primary">Registrar cliente</a>
    </div>

    <form method="GET" action="{{ route('panel.clients.index') }}" class="mb-4 flex gap-3 max-w-md">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, teléfono o correo" class="input">
        <button type="submit" class="btn-secondary shrink-0">Buscar</button>
        @if (request('q'))
            <a href="{{ route('panel.clients.index') }}" class="btn-ghost">Limpiar</a>
        @endif
    </form>

    @if ($clients->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes clientes</h2>
            <p class="text-sm text-ink-soft mt-1">Se registrarán al reservar, al registrarse voluntariamente o al crearlos aquí.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-line bg-app">
                        <th class="table-header px-5 py-3">Cliente</th>
                        <th class="table-header px-5 py-3">Contacto</th>
                        <th class="table-header px-5 py-3">Reservas</th>
                        <th class="table-header px-5 py-3">Consentimiento email</th>
                        <th class="table-header px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($clients as $client)
                        <tr>
                            <td class="px-5 py-3">
                                <p class="text-sm font-semibold">{{ $client->name }}</p>
                                @if ($client->tags->isNotEmpty())
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($client->tags as $tag)
                                            <span class="badge bg-primary-tint text-primary">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm text-ink-soft">
                                @if ($client->phone){{ $client->phone }}<br>@endif
                                @if ($client->email){{ $client->email }}@endif
                            </td>
                            <td class="px-5 py-3 text-sm">{{ $client->reservations_count }}</td>
                            <td class="px-5 py-3">
                                <span class="badge {{ $client->hasEmailConsent() ? 'bg-[#e6f6ee] text-good' : 'bg-app text-ink-soft' }}">
                                    {{ $client->hasEmailConsent() ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('panel.clients.show', $client) }}" class="text-sm font-semibold text-primary">Ver ficha</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex items-center gap-2">
            @if ($clients->previousPageUrl())
                <a href="{{ $clients->previousPageUrl() }}" class="btn-secondary px-3">← Anterior</a>
            @endif
            @if ($clients->nextPageUrl())
                <a href="{{ $clients->nextPageUrl() }}" class="btn-secondary px-3">Siguiente →</a>
            @endif
        </div>
    @endif
@endsection
