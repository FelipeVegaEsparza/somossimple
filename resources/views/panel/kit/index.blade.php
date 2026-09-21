@extends('layouts.panel')

@section('title', 'Mi kit')

@section('content')
    <div class="flex items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Mi kit</h1>
            <p class="text-sm text-ink-soft mt-1">Activa el tótem (QR + NFC) que compraste ingresando el serial que trae impreso.</p>
        </div>
    </div>

    @if (! $business)
        <div class="card p-6">
            <p class="text-sm text-ink-soft">Primero crea tu negocio para activar tu kit.</p>
        </div>
    @else
        <div class="grid lg:grid-cols-2 gap-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Activar un kit</h2>
                <p class="text-sm text-ink-soft mt-0.5">Busca el serial en la caja o etiqueta de tu tótem.</p>

                <form method="POST" action="{{ route('panel.kit.activate') }}" class="mt-4 flex gap-3">
                    @csrf
                    <input type="text" name="serial" class="input" placeholder="Por ejemplo: AL-0001" maxlength="120" required autofocus>
                    <button type="submit" class="btn-primary shrink-0">Activar kit</button>
                </form>

                <p class="mt-3 text-xs text-ink-faint">Al activarlo, tu código apuntará a tu perfil público. No administras ni descargas códigos aquí.</p>
            </div>

            <div class="card p-6 self-start">
                <h2 class="text-lg font-semibold">Tus kits activos</h2>

                @if ($kits->isEmpty())
                    <p class="mt-3 text-sm text-ink-faint">Aún no tienes kits activados.</p>
                @else
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($kits as $kit)
                            <li class="py-3 flex items-center justify-between">
                                <span class="text-sm font-medium">{{ $kit->serial }} <span class="text-ink-faint">· {{ $kit->typeLabel() }}</span></span>
                                <span class="badge bg-[#e6f6ee] text-good">Activo</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
@endsection
