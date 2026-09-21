<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\Backups\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class BackupsController extends Controller
{
    public function index(BackupService $backups): View
    {
        return view('admin.backups.index', [
            'backups' => $backups->list(),
            'mysqldump' => $backups->hasMysqlDump(),
            'mysqlClient' => $backups->hasMysqlClient(),
            'businesses' => Business::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, BackupService $backups): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:database,files,client'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id', 'required_if:type,client'],
        ], [
            'business_id.required_if' => 'Elige el cliente para el respaldo.',
            'business_id.exists' => 'El cliente seleccionado no existe.',
        ]);

        try {
            $file = match ($data['type']) {
                'database' => $backups->createDatabase(),
                'files' => $backups->createFiles(),
                'client' => $backups->createClientDatabase((int) $request->input('business_id')),
            };
        } catch (Throwable $e) {
            return back()->with('error', 'No se pudo generar el respaldo: '.$e->getMessage());
        }

        return back()->with('status', 'Respaldo generado: '.$file);
    }

    public function download(string $file, BackupService $backups): StreamedResponse
    {
        try {
            $path = $backups->pathFor($file);
        } catch (Throwable) {
            abort(404);
        }

        return response()->streamDownload(fn () => readfile($path), basename($path));
    }

    public function destroy(string $file, BackupService $backups): RedirectResponse
    {
        try {
            $backups->delete($file);
        } catch (Throwable) {
            return back()->with('error', 'No se pudo eliminar el respaldo.');
        }

        return back()->with('status', 'Respaldo eliminado.');
    }

    public function restore(Request $request, BackupService $backups): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:database,files'],
            'file' => ['required', 'file', 'max:262144'],
            'confirm' => ['accepted'],
        ], [
            'file.required' => 'Selecciona un archivo de respaldo.',
            'file.file' => 'El archivo no es válido.',
            'file.max' => 'El archivo supera el tamaño permitido (256 MB).',
            'confirm.accepted' => 'Debes confirmar que entiendes que se reemplazarán los datos actuales.',
        ]);

        $file = $request->file('file');

        try {
            if ($data['type'] === 'database') {
                if (! in_array(strtolower($file->getClientOriginalExtension()), ['sql', 'gz'], true)) {
                    throw new RuntimeException('El respaldo de base de datos debe ser un archivo .sql o .sql.gz.');
                }

                // Respaldo de seguridad antes de sobrescribir los datos actuales.
                try {
                    $backups->createDatabase();
                } catch (Throwable) {
                    // Si no se puede respaldar (p. ej. sin mysqldump), continuamos igual.
                }

                $backups->restoreDatabase($file->getRealPath());
            } else {
                if (strtolower($file->getClientOriginalExtension()) !== 'zip') {
                    throw new RuntimeException('El respaldo de archivos debe ser un archivo .zip.');
                }

                $backups->restoreFiles($file->getRealPath());
            }
        } catch (Throwable $e) {
            return back()->with('error', 'No se pudo restaurar: '.$e->getMessage());
        }

        return back()->with('status', 'Restauración completada. Si fuiste desconectado, vuelve a iniciar sesión.');
    }
}
