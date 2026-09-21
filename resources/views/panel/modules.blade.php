@extends('layouts.panel')

@section('title', 'Módulos')

@section('content')
    <div class="flex items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Mis módulos</h1>
            <p class="text-sm text-ink-soft mt-1">Cada negocio activa solo lo que necesita. Desactivar un módulo nunca elimina tus datos.</p>
        </div>
    </div>

    <livewire:modules-manager />
@endsection
