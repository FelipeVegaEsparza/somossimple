@extends('layouts.panel')

@section('title', $client->name)

@section('content')
    <div class="flex items-end justify-between gap-3 mb-6">
        <div>
            <p class="text-sm text-ink-soft">Ficha de cliente</p>
            <h1 class="text-3xl font-bold tracking-tight">{{ $client->name }}</h1>
            <p class="text-sm text-ink-soft mt-1">
                {{ $client->phone ?: 'Sin teléfono' }}{{ $client->email ? ' · '.$client->email : '' }} · Registrado el {{ $client->created_at->translatedFormat('d/m/Y') }}
            </p>
        </div>
        <a href="{{ route('panel.clients.index') }}" class="btn-ghost">← Volver</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <div class="grid lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Historial</h2>
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-app p-4">
                        <p class="text-2xl font-bold">{{ $totalReservations }}</p>
                        <p class="text-xs text-ink-soft">Reservas</p>
                    </div>
                    <div class="rounded-xl bg-app p-4">
                        <p class="text-2xl font-bold">{{ $firstReservation?->starts_at?->translatedFormat('d/m/y') ?? '—' }}</p>
                        <p class="text-xs text-ink-soft">Primera reserva</p>
                    </div>
                    <div class="rounded-xl bg-app p-4">
                        <p class="text-2xl font-bold">{{ $client->reservations->first()?->starts_at?->translatedFormat('d/m/y') ?? '—' }}</p>
                        <p class="text-xs text-ink-soft">Última reserva</p>
                    </div>
                </div>

                @if ($client->reservations->isEmpty())
                    <p class="mt-4 text-sm text-ink-faint">Sin reservas todavía.</p>
                @else
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($client->reservations as $reservation)
                            <li class="py-3 flex items-center justify-between text-sm">
                                <span>
                                    <span class="font-medium">{{ $reservation->service_name }}</span>
                                    <span class="text-ink-soft"> · {{ $reservation->starts_at->translatedFormat('d/m/Y H:i') }}</span>
                                </span>
                                <span class="text-ink-soft">{{ \App\Models\Reservation::STATUS_PENDING ? '' : '' }}{{ ucfirst($reservation->status) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Notas</h2>
                <form method="POST" action="{{ route('panel.clients.note.store', $client) }}" class="mt-4 flex gap-3">
                    @csrf
                    <input type="text" name="note" class="input" placeholder="Escribe una nota" required>
                    <button type="submit" class="btn-secondary shrink-0">Agregar</button>
                </form>

                @if ($client->notes->isNotEmpty())
                    <ul class="mt-4 space-y-2">
                        @foreach ($client->notes as $note)
                            <li class="flex items-start justify-between gap-3 rounded-xl bg-app px-4 py-3 text-sm">
                                <span>{{ $note->note }}</span>
                                <form method="POST" action="{{ route('panel.clients.note.destroy', $note) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-ink-faint hover:text-danger">✕</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Etiquetas</h2>

                @if ($client->tags->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($client->tags as $tag)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-tint text-primary px-3 h-8 text-sm font-medium">
                                {{ $tag->name }}
                                <form method="POST" action="{{ route('panel.clients.tag.detach', [$client, $tag]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Quitar etiqueta">✕</button>
                                </form>
                            </span>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('panel.clients.tag.attach', $client) }}" class="mt-4 flex gap-2">
                    @csrf
                    <input list="tags-disponibles" name="name" class="input" placeholder="Nueva o existente" required>
                    <datalist id="tags-disponibles">
                        @foreach ($availableTags as $tag)
                            <option value="{{ $tag->name }}">
                        @endforeach
                    </datalist>
                    <button type="submit" class="btn-secondary shrink-0">Agregar</button>
                </form>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Consentimiento de comunicaciones</h2>
                @php($consent = $client->consents->firstWhere('channel', 'email'))
                <p class="text-sm text-ink-soft mt-2">
                    Canal email:
                    <strong class="{{ $consent?->granted ? 'text-good' : 'text-ink-soft' }}">{{ $consent?->granted ? 'Consentido' : 'No consentido' }}</strong>
                </p>
                <p class="text-xs text-ink-faint mt-1">Ser cliente no implica recibir promociones. El consentimiento es independiente.</p>

                <form method="POST" action="{{ route('panel.clients.consent', $client) }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="granted" value="{{ $consent?->granted ? 0 : 1 }}">
                    <button type="submit" class="btn-secondary w-full">
                        {{ $consent?->granted ? 'Retirar consentimiento' : 'Otorgar consentimiento' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
