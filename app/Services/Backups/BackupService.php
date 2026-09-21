<?php

namespace App\Services\Backups;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

/**
 * Respaldos del sistema para el panel de administración: base de datos
 * (mysqldump) y archivos subidos (ZIP del disco público).
 */
class BackupService
{
    public function dir(): string
    {
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir;
    }

    public function hasMysqlDump(): bool
    {
        return $this->hasCommand('mysqldump');
    }

    public function hasMysqlClient(): bool
    {
        return $this->hasCommand('mysql');
    }

    private function hasCommand(string $command): bool
    {
        try {
            $process = Process::fromShellCommandline('command -v '.escapeshellarg($command));
            $process->run();

            return $process->isSuccessful();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Detecta si un archivo es gzip por sus bytes mágicos (el archivo subido
     * no conserva la extensión original).
     */
    public function isGzip(string $path): bool
    {
        $handle = @fopen($path, 'rb');

        if ($handle === false) {
            return false;
        }

        $magic = fread($handle, 2);
        fclose($handle);

        return $magic === "\x1f\x8b";
    }

    /**
     * @return Collection<int, array{name: string, size: int, created_at: Carbon, type: string}>
     */
    public function list(): Collection
    {
        return collect(glob($this->dir().'/*'))
            ->filter(fn (string $path) => is_file($path))
            ->map(function (string $path) {
                $name = basename($path);

                return [
                    'name' => $name,
                    'size' => (int) filesize($path),
                    'created_at' => Carbon::createFromTimestamp((int) filemtime($path)),
                    'type' => str_contains($name, '-db-') ? 'database' : 'files',
                ];
            })
            ->sortByDesc('created_at')
            ->values();
    }

    public function pathFor(string $name): string
    {
        $name = basename($name);

        if (! preg_match('/^[A-Za-z0-9._-]+\.(sql\.gz|zip)$/', $name)) {
            throw new RuntimeException('Nombre de respaldo inválido.');
        }

        $path = $this->dir().'/'.$name;

        if (! is_file($path)) {
            throw new RuntimeException('El respaldo no existe.');
        }

        return $path;
    }

    public function delete(string $name): void
    {
        @unlink($this->pathFor($name));
    }

    /**
     * Respaldo de la base de datos con mysqldump (comprimido .sql.gz).
     */
    public function createDatabase(): string
    {
        if (! $this->hasMysqlDump()) {
            throw new RuntimeException('mysqldump no está instalado en el servidor.');
        }

        $config = config('database.connections.mysql');
        $file = 'backup-db-'.now()->format('Ymd-His').'.sql.gz';
        $path = $this->dir().'/'.$file;

        $command = [
            'mysqldump',
            '--host='.$config['host'],
            '--port='.(string) $config['port'],
            '--user='.$config['username'],
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--no-tablespaces',
            '--routines',
            $config['database'],
        ];

        $handle = gzopen($path, 'wb');
        $stderr = '';

        try {
            $process = new Process($command);
            $process->setEnv(['MYSQL_PWD' => (string) $config['password']]);
            $process->setTimeout(900);
            $process->run(function (string $type, string $buffer) use ($handle, &$stderr) {
                if ($type === Process::ERR) {
                    $stderr .= $buffer;
                } else {
                    gzwrite($handle, $buffer);
                }
            });
        } catch (Throwable $e) {
            gzclose($handle);
            @unlink($path);

            throw new RuntimeException($e->getMessage());
        }

        gzclose($handle);

        if (! $process->isSuccessful()) {
            @unlink($path);

            throw new RuntimeException(trim($stderr) ?: 'mysqldump terminó con error.');
        }

        return $file;
    }

    /**
     * Respaldo de los archivos subidos (ZIP del disco público).
     */
    public function createFiles(?string $source = null): string
    {
        $source = $source ?? storage_path('app/public');
        $file = 'backup-files-'.now()->format('Ymd-His').'.zip';
        $path = $this->dir().'/'.$file;

        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo crear el archivo ZIP.');
        }

        $added = 0;

        if (is_dir($source)) {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($items as $item) {
                if ($item->isFile()) {
                    $relative = ltrim(str_replace($source, '', $item->getPathname()), '/');
                    $zip->addFile($item->getPathname(), $relative);
                    $added++;
                }
            }
        }

        $zip->close();

        if ($added === 0) {
            @unlink($path);

            throw new RuntimeException('Todavía no hay archivos subidos para respaldar.');
        }

        if (! is_file($path)) {
            throw new RuntimeException('No se pudo crear el archivo ZIP.');
        }

        return $file;
    }

    /**
     * Restaura la base de datos desde un archivo .sql o .sql.gz.
     */
    public function restoreDatabase(string $sourcePath): void
    {
        if (! $this->hasMysqlClient()) {
            throw new RuntimeException('El cliente mysql no está instalado en el servidor.');
        }

        $config = config('database.connections.mysql');

        $sqlPath = $sourcePath;
        $temp = null;

        if ($this->isGzip($sourcePath)) {
            $temp = tempnam(sys_get_temp_dir(), 'restore_').'.sql';
            $in = gzopen($sourcePath, 'rb');
            $out = fopen($temp, 'wb');

            while (! gzeof($in)) {
                fwrite($out, gzread($in, 1 << 20));
            }

            gzclose($in);
            fclose($out);

            $sqlPath = $temp;
        }

        $command = [
            'mysql',
            '--host='.$config['host'],
            '--port='.(string) $config['port'],
            '--user='.$config['username'],
            $config['database'],
        ];

        $input = fopen($sqlPath, 'rb');

        try {
            $process = new Process($command);
            $process->setEnv(['MYSQL_PWD' => (string) $config['password']]);
            $process->setTimeout(900);
            $process->setInput($input);
            $process->run();
        } finally {
            if (is_resource($input)) {
                fclose($input);
            }
            if ($temp) {
                @unlink($temp);
            }
        }

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'La restauración de la base de datos falló.');
        }
    }

    /**
     * Restaura los archivos subidos desde un ZIP. Devuelve cuántos archivos extrajo.
     */
    public function restoreFiles(string $zipPath): int
    {
        $target = storage_path('app/public');

        if (! is_dir($target)) {
            mkdir($target, 0775, true);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('No se pudo abrir el archivo ZIP.');
        }

        $extracted = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            $clean = str_replace('\\', '/', $name);

            // Evita rutas absolutas o saltos de directorio (zip-slip).
            if ($clean === '' || str_starts_with($clean, '/') || str_contains($clean, '..')) {
                continue;
            }

            if (str_ends_with($clean, '/')) {
                continue;
            }

            $destination = $target.'/'.$clean;
            $directory = dirname($destination);

            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $stream = $zip->getStream($name);

            if ($stream === false) {
                continue;
            }

            $out = fopen($destination, 'wb');

            while (! feof($stream)) {
                fwrite($out, fread($stream, 1 << 20));
            }

            fclose($out);
            fclose($stream);
            $extracted++;
        }

        $zip->close();

        if ($extracted === 0) {
            throw new RuntimeException('El ZIP no contenía archivos válidos.');
        }

        return $extracted;
    }
}
