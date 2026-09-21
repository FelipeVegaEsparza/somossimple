<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Backups\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(storage_path('app/backups'));
        File::cleanDirectory(storage_path('app/backups'));
    }

    private function admin(): User
    {
        return User::factory()->create(['is_platform_admin' => true]);
    }

    public function test_solo_el_admin_accede_a_respaldos(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.backups.index'))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('admin.backups.index'))
            ->assertOk()
            ->assertSee('Respaldos');
    }

    public function test_genera_lista_descarga_y_elimina_un_respaldo_de_archivos(): void
    {
        $admin = $this->admin();
        File::put(storage_path('app/public/demo.txt'), 'hola');

        $this->actingAs($admin)->post(route('admin.backups.store'), ['type' => 'files'])
            ->assertRedirect()
            ->assertSessionHas('status');

        $files = glob(storage_path('app/backups').'/*.zip');
        $this->assertNotEmpty($files);
        $name = basename($files[0]);

        $this->actingAs($admin)->get(route('admin.backups.index'))->assertSee($name);

        $this->actingAs($admin)->get(route('admin.backups.download', $name))
            ->assertOk()
            ->assertDownload($name);

        $this->actingAs($admin)->delete(route('admin.backups.destroy', $name))->assertRedirect();
        $this->assertFileDoesNotExist(storage_path('app/backups/'.$name));
    }

    public function test_avisa_cuando_no_hay_mysqldump_para_la_base_de_datos(): void
    {
        if (app(BackupService::class)->hasMysqlDump()) {
            $this->markTestSkipped('mysqldump disponible en este entorno.');
        }

        $this->actingAs($this->admin())->post(route('admin.backups.store'), ['type' => 'database'])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_no_permite_descargar_nombres_invalidos(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.backups.download', 'archivo.txt'))
            ->assertNotFound();
    }

    public function test_avisa_cuando_no_hay_archivos_para_respaldar(): void
    {
        $empty = storage_path('app/testing-empty');
        File::ensureDirectoryExists($empty);
        File::cleanDirectory($empty);

        $this->expectException(\RuntimeException::class);

        app(BackupService::class)->createFiles($empty);
    }

    public function test_restaura_archivos_desde_un_zip(): void
    {
        File::delete(storage_path('app/public/restore-demo.txt'));

        $zipPath = tempnam(sys_get_temp_dir(), 'zip').'.zip';
        $zip = new \ZipArchive;
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('restore-demo.txt', 'contenido restaurado');
        $zip->close();

        $upload = new UploadedFile($zipPath, 'backup-files.zip', 'application/zip', null, true);

        $this->actingAs($this->admin())->post(route('admin.backups.restore'), [
            'type' => 'files',
            'file' => $upload,
            'confirm' => '1',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertFileExists(storage_path('app/public/restore-demo.txt'));
        $this->assertEquals('contenido restaurado', file_get_contents(storage_path('app/public/restore-demo.txt')));

        File::delete(storage_path('app/public/restore-demo.txt'));
    }

    public function test_ignora_rutas_maliciosas_al_restaurar_un_zip(): void
    {
        File::delete(storage_path('app/public/ok.txt'));
        File::delete(storage_path('app/evil.txt'));

        $zipPath = tempnam(sys_get_temp_dir(), 'zip').'.zip';
        $zip = new \ZipArchive;
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('ok.txt', 'ok');
        $zip->addFromString('../evil.txt', 'malicioso');
        $zip->close();

        $upload = new UploadedFile($zipPath, 'backup-files.zip', 'application/zip', null, true);

        $this->actingAs($this->admin())->post(route('admin.backups.restore'), [
            'type' => 'files',
            'file' => $upload,
            'confirm' => '1',
        ])->assertRedirect();

        $this->assertFileExists(storage_path('app/public/ok.txt'));
        $this->assertFileDoesNotExist(storage_path('app/evil.txt'));

        File::delete(storage_path('app/public/ok.txt'));
    }

    public function test_requiere_confirmacion_para_restaurar(): void
    {
        $upload = UploadedFile::fake()->createWithContent('backup.sql', 'SELECT 1;');

        $this->actingAs($this->admin())->post(route('admin.backups.restore'), [
            'type' => 'database',
            'file' => $upload,
        ])->assertSessionHasErrors('confirm');
    }

    public function test_restaurar_base_de_datos_avisa_si_no_hay_cliente_mysql(): void
    {
        if (app(BackupService::class)->hasMysqlClient()) {
            $this->markTestSkipped('Cliente mysql disponible en este entorno.');
        }

        $upload = UploadedFile::fake()->createWithContent('backup.sql', 'SELECT 1;');

        $this->actingAs($this->admin())->post(route('admin.backups.restore'), [
            'type' => 'database',
            'file' => $upload,
            'confirm' => '1',
        ])->assertRedirect()->assertSessionHas('error');
    }

    public function test_detecta_archivos_gzip_por_contenido(): void
    {
        $service = app(BackupService::class);

        $plain = tempnam(sys_get_temp_dir(), 'sql');
        file_put_contents($plain, 'SELECT 1;');
        $this->assertFalse($service->isGzip($plain));

        $gz = tempnam(sys_get_temp_dir(), 'gz');
        $handle = gzopen($gz, 'wb');
        gzwrite($handle, 'SELECT 1;');
        gzclose($handle);
        $this->assertTrue($service->isGzip($gz));

        @unlink($plain);
        @unlink($gz);
    }
}
