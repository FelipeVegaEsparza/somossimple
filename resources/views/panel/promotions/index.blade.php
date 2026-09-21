@extends('layouts.panel')

@section('title', 'Promociones')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Promociones</h1>
            <p class="text-sm text-ink-soft mt-1">Muestra ofertas y descuentos donde tus clientes ya están mirando: tu perfil y tus puntos QR/NFC.</p>
        </div>
        <a href="{{ route('panel.promotions.create') }}" class="btn-primary">Nueva promoción</a>
    </div>

    @if ($promotions->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no tienes promociones</h2>
            <p class="text-sm text-ink-soft mt-1">Crea tu primera oferta para atraer a tus clientes.</p>
            <a href="{{ route('panel.promotions.create') }}" class="btn-primary mt-5">Crear promoción</a>
        </div>
    @else
        @include('panel.promotions._list', ['promotions' => $promotions])
    @endif
@endsection
