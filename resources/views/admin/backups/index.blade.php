@extends('admin.layouts.admin')

@section('title', 'Respaldos')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight">Respaldos</h1>
        <p class="text-sm text-ink-soft mt-1">Genera y descarga copias de la base de datos y de los archivos subidos.</p>
    </div>

    @if (! $mysqldump)
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">
            <strong>mysqldump no está disponible</strong> en el servidor, así que el respaldo de base de datos no se puede generar.
            Reconstruye la imagen (incluye <code>default-mysql-client</code>) para habilitarlo.
        </div>
    @endif

    <div class="card p-6">
        <h2 class="text-lg font-semibold">Generar respaldo</h2>
        <p class="text-sm text-ink-soft mt-0.5">El respaldo se guarda en el servidor y queda disponible para descargar.</p>

        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.backups.store') }}" onsubmit="return confirm('¿Generar respaldo de la base de datos?')">
                @csrf
                <input type="hidden" name="type" value="database">
                <button type="submit" class="btn-primary" @disabled(! $mysqldump)>Respaldo de base de datos</button>
            </form>
            <form method="POST" action="{{ route('admin.backups.store') }}" onsubmit="return confirm('¿Generar respaldo de los archivos subidos? Puede tardar si hay muchas imágenes.')">
                @csrf
                <input type="hidden" name="type" value="files">
                <button type="submit" class="btn-secondary">Respaldo de archivos (ZIP)</button>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden mt-4">
        @if ($backups->isEmpty())
            <div class="p-8 text-center">
                <h2 class="text-lg font-semibold">Todavía no hay respaldos</h2>
                <p class="text-sm text-ink-soft mt-1">Genera el primero con los botones de arriba.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-app">
                        <tr>
                            <th class="table-header px-4 py-3">Archivo</th>
                            <th class="table-header px-4 py-3">Tipo</th>
                            <th class="table-header px-4 py-3 text-right">Tamaño</th>
                            <th class="table-header px-4 py-3">Fecha</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($backups as $backup)
                            @php
                                $size = $backup['size'];
                                $readable = $size >= 1048576
                                    ? number_format($size / 1048576, 1, ',', '.').' MB'
                                    : ($size >= 1024 ? number_format($size / 1024, 0, ',', '.').' KB' : $size.' B');
                            @endphp
                            <tr class="hover:bg-app/60">
                                <td class="px-4 py-3 font-mono text-xs">{{ $backup['name'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $backup['type'] === 'database' ? 'bg-primary-tint text-primary' : 'bg-app text-ink-soft' }}">
                                        {{ $backup['type'] === 'database' ? 'Base de datos' : 'Archivos' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">{{ $readable }}</td>
                                <td class="px-4 py-3 text-ink-soft whitespace-nowrap">{{ $backup['created_at']->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.backups.download', $backup['name']) }}" class="text-sm font-semibold text-primary">Descargar</a>
                                    <form method="POST" action="{{ route('admin.backups.destroy', $backup['name']) }}" class="inline ml-3" onsubmit="return confirm('¿Eliminar este respaldo?')">
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
        @endif
    </div>

    <p class="mt-5 text-xs text-ink-faint">
        Los respaldos quedan en <code>storage/app/backups</code> (volumen persistente). Contienen datos sensibles: descárgalos a un lugar seguro y elimínalos del servidor cuando ya no los necesites.
    </p>
@endsection
