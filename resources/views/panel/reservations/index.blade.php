@extends('layouts.panel')

@section('title', 'Reservas')

@section('content')
    @php($prev = $date->copy()->subDay()->toDateString())
    @php($next = $date->copy()->addDay()->toDateString())

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Reservas</h1>
            <p class="text-sm text-ink-soft mt-1">La agenda de tu negocio.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('panel.reservations.index', ['date' => $prev]) }}" class="btn-secondary px-3">←</a>
            <span class="text-sm font-semibold min-w-[10rem] text-center">{{ $date->translatedFormat('l d F Y') }}</span>
            <a href="{{ route('panel.reservations.index', ['date' => $next]) }}" class="btn-secondary px-3">→</a>
            <a href="{{ route('panel.reservations.index', ['date' => $date->toDateString(), 'view' => 'day']) }}" class="btn-ghost {{ $view === 'day' ? 'text-primary' : '' }}">Día</a>
            <a href="{{ route('panel.reservations.index', ['date' => $date->toDateString(), 'view' => 'week']) }}" class="btn-ghost {{ $view === 'week' ? 'text-primary' : '' }}">Semana</a>
            <a href="{{ route('panel.reservations.config') }}" class="btn-primary">Configurar reservas</a>
        </div>
    </div>

    @if ($view === 'week')
        @php($isEmptyWeek = collect($reservations)->flatten(1)->isEmpty())
        @if ($isEmptyWeek)
            <div class="card p-8 text-center">
                <h2 class="text-lg font-semibold">Sin reservas esta semana</h2>
            </div>
        @else
            @foreach ($reservations as $dayKey => $dayReservations)
                <div class="mb-5">
                    <h2 class="text-sm font-bold mb-2">{{ \Carbon\Carbon::parse($dayKey)->translatedFormat('l d F Y') }}</h2>
                    @include('panel.reservations._table', ['reservations' => $dayReservations, 'statusLabels' => $statusLabels])
                </div>
            @endforeach
        @endif
    @else
        @if ($reservations->isEmpty())
            <div class="card p-8 text-center">
                <h2 class="text-lg font-semibold">Sin reservas este día</h2>
                <p class="text-sm text-ink-soft mt-1">Las solicitudes de tus clientes aparecerán aquí.</p>
            </div>
        @else
            @include('panel.reservations._table', ['reservations' => $reservations, 'statusLabels' => $statusLabels])
        @endif
    @endif
@endsection
