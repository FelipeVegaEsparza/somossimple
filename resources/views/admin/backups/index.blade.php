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

        <form method="POST" action="{{ route('admin.backups.store') }}" class="mt-5 border-t border-line pt-5 flex flex-wrap items-end gap-3"
              onsubmit="return confirm('¿Generar respaldo de este cliente?')">
            @csrf
            <input type="hidden" name="type" value="client">
            <div class="flex-1 min-w-[16rem]">
                <label for="business_id" class="label">Respaldo de un cliente</label>
                <select id="business_id" name="business_id" class="input" required>
                    <option value="">Elige un cliente…</option>
                    @foreach ($businesses as $business)
                        <option value="{{ $business->id }}" @selected(old('business_id') == $business->id)>{{ $business->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary shrink-0" @disabled(! $mysqldump)>Respaldo del cliente</button>
        </form>
        <p class="mt-2 text-xs text-ink-soft">
            Incluye la ficha del negocio, su cuenta y todos sus datos (catálogo, clientes, reservas, fidelización, ticketera, etc.). No incluye archivos.
        </p>
    </div>

    <div class="card p-6 mt-4 border-danger/30">
        <h2 class="text-lg font-semibold text-danger">Restaurar respaldo</h2>
        <p class="text-sm text-ink-soft mt-0.5">
            Sube un respaldo para <strong>reemplazar los datos actuales</strong>. Úsalo solo para recuperarte de un problema.
        </p>
        <p class="mt-2 rounded-xl bg-red-50 px-4 py-3 text-xs text-danger">
            Operación destructiva: la base de datos (o los archivos) actuales serán reemplazados por el contenido del respaldo. Antes de restaurar la base se genera un respaldo de seguridad automáticamente.
        </p>

        <div class="mt-4 grid sm:grid-cols-2 gap-4">
            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data" class="rounded-xl border border-line p-4"
                  onsubmit="return confirm('¿Restaurar la base de datos? Se reemplazarán los datos actuales.')">
                @csrf
                <input type="hidden" name="type" value="database">
                <h3 class="font-semibold text-sm">Base de datos</h3>
                <p class="text-xs text-ink-soft mt-0.5">Archivo <code>.sql</code> o <code>.sql.gz</code>.</p>
                <label class="mt-3 block text-sm">
                    <input type="file" name="file" accept=".sql,.gz" required class="input @error('file') input-error @enderror">
                </label>
                <label class="mt-3 flex items-start gap-2 text-xs text-ink-soft cursor-pointer">
                    <input type="checkbox" name="confirm" value="1" required class="mt-0.5 w-4 h-4 rounded border-line text-danger accent-danger">
                    <span>Entiendo que se reemplazarán los datos actuales.</span>
                </label>
                <button type="submit" class="btn-secondary border-danger/40 text-danger mt-3 w-full" @disabled(! $mysqlClient)>
                    Restaurar base de datos
                </button>
                @if (! $mysqlClient)
                    <p class="mt-2 text-xs text-danger">El cliente <code>mysql</code> no está disponible en el servidor.</p>
                @endif
            </form>

            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data" class="rounded-xl border border-line p-4"
                  onsubmit="return confirm('¿Restaurar los archivos? Se sobrescribirán los archivos con el mismo nombre.')">
                @csrf
                <input type="hidden" name="type" value="files">
                <h3 class="font-semibold text-sm">Archivos subidos</h3>
                <p class="text-xs text-ink-soft mt-0.5">Archivo <code>.zip</code> generado por esta sección.</p>
                <label class="mt-3 block text-sm">
                    <input type="file" name="file" accept=".zip" required class="input @error('file') input-error @enderror">
                </label>
                <label class="mt-3 flex items-start gap-2 text-xs text-ink-soft cursor-pointer">
                    <input type="checkbox" name="confirm" value="1" required class="mt-0.5 w-4 h-4 rounded border-line text-danger accent-danger">
                    <span>Entiendo que se sobrescribirán los archivos actuales.</span>
                </label>
                <button type="submit" class="btn-secondary border-danger/40 text-danger mt-3 w-full">
                    Restaurar archivos
                </button>
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
                                    <span class="badge {{ match ($backup['type']) {
                                        'database' => 'bg-primary-tint text-primary',
                                        'client' => 'bg-[#fff6e0] text-warn',
                                        default => 'bg-app text-ink-soft',
                                    } }}">
                                        {{ match ($backup['type']) {
                                            'database' => 'Base de datos',
                                            'client' => 'Cliente',
                                            default => 'Archivos',
                                        } }}
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
