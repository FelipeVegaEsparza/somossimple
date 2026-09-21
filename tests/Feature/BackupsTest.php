<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Backups\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
