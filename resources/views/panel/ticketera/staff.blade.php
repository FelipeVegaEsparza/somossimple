@extends('layouts.panel')

@section('title', 'Personal de acceso')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Ticketera</p>
        <h1 class="text-3xl font-bold tracking-tight">Personal de acceso</h1>
        <p class="text-sm text-ink-soft mt-1">Cada persona recibe un enlace para escanear entradas, sin acceso al panel.</p>
    </div>

    @include('panel.ticketera._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('panel.ticketera.staff.store') }}" class="card p-6 max-w-xl flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-[12rem]">
            <label for="name" class="label">Nombre</label>
            <input id="name" type="text" name="name" required class="input">
        </div>
        <div class="flex-1 min-w-[12rem]">
            <label for="email" class="label">Correo (opcional)</label>
            <input id="email" type="email" name="email" class="input">
        </div>
        <button type="submit" class="btn-primary shrink-0">Agregar</button>
    </form>

    @if ($staff->isEmpty())
        <div class="card p-8 text-center mt-4">
            <p class="text-sm text-ink-soft">Aún no has agregado personal de acceso.</p>
        </div>
    @else
        <div class="card overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Nombre</th>
                            <th class="table-header px-4 py-3">Enlace de acceso</th>
                            <th class="table-header px-4 py-3">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($staff as $person)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $person->name }}</p>
                                    @if ($person->email)<p class="text-xs text-ink-soft">{{ $person->email }}</p>@endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('ticketera.access', $person->token) }}" target="_blank" rel="noopener" class="break-all font-mono text-xs text-primary">{{ route('ticketera.access', $person->token) }}</a>
                                </td>
                                <td class="px-4 py-3"><span class="badge {{ $person->is_active ? 'bg-good/15 text-good' : 'bg-app text-ink-soft' }}">{{ $person->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('panel.ticketera.staff.toggle', $person) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm font-semibold text-ink-soft">{{ $person->is_active ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('panel.ticketera.staff.destroy', $person) }}" class="inline ml-3" onsubmit="return confirm('¿Eliminar este acceso?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
