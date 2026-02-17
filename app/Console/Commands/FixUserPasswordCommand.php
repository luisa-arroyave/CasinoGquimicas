<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixUserPasswordCommand extends Command
{
    protected $signature = 'user:fix-password {email : Email del usuario} {password : Nueva contraseña}';

    protected $description = 'Establecer o corregir la contraseña de un usuario en la tabla users (para poder iniciar sesión en la web)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No existe un usuario con el email: {$email}");
            $this->line('Recuerda: el login web usa la tabla <comment>users</comment>, no la tabla usuarios.');
            $this->line('Crea uno con: php artisan user:create --email=' . $email . ' --name="Tu Nombre" --password="' . $password . '" --role=empleado');
            return self::FAILURE;
        }

        $user->update(['password' => Hash::make($password)]);

        $this->info("Contraseña actualizada para: {$email}");
        $this->line('Ya puedes iniciar sesión en la web con ese email y la nueva contraseña.');
        return self::SUCCESS;
    }
}
