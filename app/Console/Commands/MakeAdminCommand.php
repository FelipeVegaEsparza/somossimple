<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeAdminCommand extends Command
{
    protected $signature = 'admin:make {email} {--name=} {--password=}';

    protected $description = 'Crea una cuenta o promueve una existente como administrador de la plataforma.';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $password = $this->option('password');

        $user = User::where('email', $email)->first();

        if ($user) {
            $data = ['is_platform_admin' => true];

            if ($password) {
                $data['password'] = Hash::make($password);
            }

            $user->update($data);

            $this->info("Listo: {$user->email} es administrador de la plataforma.");

            return self::SUCCESS;
        }

        $name = $this->option('name');

        if (! $name || ! $password) {
            $this->error('El usuario no existe. Créalo con:');
            $this->line('  php artisan admin:make correo@dominio.cl --name="Nombre" --password="clave-segura"');

            return self::FAILURE;
        }

        $validator = Validator::make(
            ['email' => $email, 'name' => $name, 'password' => $password],
            [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_platform_admin' => true,
        ]);

        $this->info("Administrador creado: {$user->email}");

        return self::SUCCESS;
    }
}
