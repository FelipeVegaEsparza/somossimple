@extends('layouts.panel')

@section('title', 'Eventos')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Eventos</h1>
            <p class="text-sm text-ink-soft mt-1">Publica actividades, cursos, talleres y lanzamientos. Tus clientes pueden llegar desde un QR o NFC.</p>
        </div>
        <a href="{{ route('panel.events.create') }}" class="btn-primary">Nuevo evento</a>
    </div>

    @if ($events->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes eventos</h2>
            <p class="text-sm text-ink-soft mt-1">Agrega tu próxima actividad para que tus clientes la conozcan.</p>
            <a href="{{ route('panel.events.create') }}" class="btn-primary mt-5">Crear evento</a>
        </div>
    @else
        @include('panel.events._list', ['events' => $events])
    @endif
@endsection
