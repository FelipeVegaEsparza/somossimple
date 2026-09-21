<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AccountFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_exitoso_crea_cuenta_y_la_deja_con_sesion(): void
    {
        $response = $this->post('/register', [
            'name' => 'María Pérez',
            'email' => 'maria@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('panel.index'));

        $this->assertDatabaseHas('users', ['email' => 'maria@example.com']);
        $this->assertAuthenticated();
    }

    public function test_registro_con_correo_ya_utilizado_se_rechaza(): void
    {
        User::factory()->create(['email' => 'maria@example.com']);

        $response = $this->post('/register', [
            'name' => 'María Pérez',
            'email' => 'maria@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inicio_de_sesion_con_credenciales_correctas(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('panel.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inicio_de_sesion_con_contrasena_incorrecta_se_rechaza(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_cierre_de_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_recuperacion_no_revela_si_el_correo_existe(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'no-existe@example.com']);

        $response->assertSessionHas('status');
        $this->assertTrue(str_contains(session('status'), 'Si el correo está registrado'));
    }

    public function test_recuperacion_envia_enlace_y_permite_restablecer(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'nueva1234',
            'password_confirmation' => 'nueva1234',
        ])->assertRedirect(route('login'));

        $this->assertTrue(\Illuminate\Support\Facades\Auth::attempt([
            'email' => $user->email,
            'password' => 'nueva1234',
        ]));
    }

    public function test_una_cuenta_solo_puede_tener_un_negocio(): void
    {
        $user = User::factory()->create();
        Business::createForAccount($user, 'Barbería Patagonia');

        $response = $this->actingAs($user)->post('/panel/negocio', [
            'name' => 'Otro negocio',
        ]);

        $response->assertRedirect(route('panel.index'));
        $this->assertEquals(1, Business::where('account_id', $user->id)->count());
    }
}
