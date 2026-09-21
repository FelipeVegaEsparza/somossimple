@extends('layouts.panel')

@section('title', 'Comunicaciones')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Comunicaciones</h1>
            <p class="text-sm text-ink-soft mt-1">Envía por correo promociones y avisos a tus clientes, respetando su consentimiento.</p>
        </div>
        <a href="{{ route('panel.communications.create') }}" class="btn-primary">Nueva comunicación</a>
    </div>

    @if ($communications->isEmpty())
        <div class="card p-8 text-center">
            <h2 class="text-lg font-semibold">Aún no creas comunicaciones</h2>
            <p class="text-sm text-ink-soft mt-1">Promociones, cambios de horario, anuncios… empieza creando una.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-line bg-app">
                        <th class="table-header px-5 py-3">Comunicación</th>
                        <th class="table-header px-5 py-3">Tipo</th>
                        <th class="table-header px-5 py-3">Destinatarios</th>
                        <th class="table-header px-5 py-3">Estado</th>
                        <th class="table-header px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($communications as $communication)
                        <tr>
                            <td class="px-5 py-3">
                                <p class="text-sm font-semibold">{{ $communication->subject }}</p>
                                <p class="text-xs text-ink-faint">Creada el {{ $communication->created_at->translatedFormat('d/m/Y') }}</p>
                            </td>
                            <td class="px-5 py-3 text-sm text-ink-soft">
                                {{ $communication->typeLabel() }}<br>
                                <span class="text-xs">{{ $communication->is_commercial ? 'Comercial' : 'Operacional' }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm text-ink-soft">
                                {{ $communication->audience === 'tag' ? 'Etiqueta «'.$communication->tag_name.'»' : 'Todos con consentimiento' }}
                                · {{ $communication->sends_count }} enviados
                            </td>
                            <td class="px-5 py-3">
                                <span class="badge {{ $communication->status === 'sent' ? 'bg-[#e6f6ee] text-good' : 'bg-[#fff6e0] text-warn' }}">
                                    {{ $communication->status === 'sent' ? 'Enviada' : 'Borrador' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if ($communication->status === 'draft')
                                    <form method="POST" action="{{ route('panel.communications.send', $communication) }}" onsubmit="return confirm('¿Enviar esta comunicación ahora?')">
                                        @csrf
                                        <button type="submit" class="btn-primary">Enviar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
