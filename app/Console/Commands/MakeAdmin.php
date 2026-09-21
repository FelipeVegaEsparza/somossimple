<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'app:make-admin {email} {--remove : Quitar el rol de administrador}';

    protected $description = 'Marca (o desmarca con --remove) una cuenta como administrador de la plataforma';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No existe una cuenta con el correo {$email}.");

            return self::FAILURE;
        }

        if ($this->option('remove')) {
            $admins = User::where('is_platform_admin', true)->count();

            if ($user->is_platform_admin && $admins <= 1) {
                $this->error('No puedes quitar el último administrador: la plataforma quedaría sin acceso.');

                return self::FAILURE;
            }

            $user->update(['is_platform_admin' => false]);
            $this->info("Rol de administrador quitado de {$email}.");

            return self::SUCCESS;
        }

        $user->update(['is_platform_admin' => true]);
        $this->info("{$email} ahora es administrador de la plataforma.");

        return self::SUCCESS;
    }
}
