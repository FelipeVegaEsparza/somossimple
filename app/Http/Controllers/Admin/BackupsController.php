<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Backups\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class BackupsController extends Controller
{
    public function index(BackupService $backups): View
    {
        return view('admin.backups.index', [
            'backups' => $backups->list(),
            'mysqldump' => $backups->hasMysqlDump(),
        ]);
    }

    public function store(Request $request, BackupService $backups): RedirectResponse
    {
        $type = $request->validate([
            'type' => ['required', 'in:database,files'],
        ])['type'];

        try {
            $file = $type === 'database'
                ? $backups->createDatabase()
                : $backups->createFiles();
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
}
