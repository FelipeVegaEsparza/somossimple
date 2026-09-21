<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_promueve_una_cuenta_existente(): void
    {
        $user = User::factory()->create(['is_platform_admin' => false]);

        $this->artisan('admin:make', ['email' => $user->email])->assertExitCode(0);

        $this->assertTrue($user->fresh()->is_platform_admin);
    }

    public function test_crea_un_administrador_nuevo(): void
    {
        $this->artisan('admin:make', [
            'email' => 'admin@somossimple.cl',
            '--name' => 'Admin',
            '--password' => 'clave-segura-123',
        ])->assertExitCode(0);

        $user = User::where('email', 'admin@somossimple.cl')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_platform_admin);
    }

    public function test_falla_si_no_existe_y_faltan_datos(): void
    {
        $this->artisan('admin:make', ['email' => 'nadie@somossimple.cl'])->assertExitCode(1);

        $this->assertNull(User::where('email', 'nadie@somossimple.cl')->first());
    }
}
