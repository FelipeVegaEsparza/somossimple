@extends('layouts.panel')

@section('title', 'Servicios')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Servicios</h1>
            <p class="text-sm text-ink-soft mt-1">Explica qué haces y cuánto cuesta antes de que tus clientes tengan que preguntar.</p>
        </div>
        <a href="{{ route('panel.services.create') }}" class="btn-primary">Nuevo servicio</a>
    </div>

    @if ($services->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes servicios</h2>
            <p class="text-sm text-ink-soft mt-1">Agrega tus servicios, precios y duración para mostrarlos en tu perfil.</p>
            <a href="{{ route('panel.services.create') }}" class="btn-primary mt-5">Crear servicio</a>
        </div>
    @else
        @include('panel.services._list', ['services' => $services])
    @endif
@endsection
