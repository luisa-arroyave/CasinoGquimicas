<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {--name= : Nombre del usuario}
                            {--email= : Email (obligatorio)}
                            {--password= : Contraseña (si no se da, se pedirá)}
                            {--role=empleado : Rol (administrador, gestionhumana, casino, operativo, empleado)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un usuario en la base de datos (tabla users)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $roles = config('roles.all', ['administrador', 'gestionhumana', 'casino', 'operativo', 'empleado']);
        $name = $this->option('name') ?: $this->ask('Nombre');
        $email = $this->option('email') ?: $this->ask('Email');
        $role = $this->option('role') ?: $this->choice('Rol', $roles, 'empleado');

        if (! $name || ! $email) {
            $this->error('Nombre y email son obligatorios.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Ya existe un usuario con el email: {$email}");
            return self::FAILURE;
        }

        if (! in_array($role, $roles, true)) {
            $this->error("Rol no válido. Use uno de: " . implode(', ', $roles));
            return self::FAILURE;
        }

        $password = $this->option('password');
        if (! $password) {
            $password = $this->secret('Contraseña');
            $passwordConfirmation = $this->secret('Confirmar contraseña');
            if ($password !== $passwordConfirmation) {
                $this->error('Las contraseñas no coinciden.');
                return self::FAILURE;
            }
        }

        $validator = validator(
            ['password' => $password],
            ['password' => [Password::defaults()]]
        );
        if ($validator->fails()) {
            $this->error($validator->errors()->first('password'));
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->info("Usuario creado correctamente: {$email} (rol: {$role})");
        return self::SUCCESS;
    }
}
