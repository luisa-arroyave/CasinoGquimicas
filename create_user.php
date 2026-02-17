#!/usr/bin/env php
<?php
/**
 * Script para crear un usuario en la BD (tabla users).
 * Uso (desde la raíz del proyecto):
 *   php create_user.php "Nombre" email@ejemplo.com contraseña [rol]
 *   php create_user.php "Admin" admin@casino.com secret123 administrador
 *
 * Roles: administrador, gestionhumana, casino, operativo, empleado (por defecto)
 */

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$name     = $argv[1] ?? null;
$email    = $argv[2] ?? null;
$password = $argv[3] ?? null;
$role     = $argv[4] ?? 'empleado';

$roles = ['administrador', 'gestionhumana', 'casino', 'operativo', 'empleado'];

if (! $name || ! $email || ! $password) {
    fwrite(STDERR, "Uso: php create_user.php \"Nombre\" email@ejemplo.com contraseña [rol]\n");
    fwrite(STDERR, "Roles: " . implode(', ', $roles) . "\n");
    exit(1);
}

if (! in_array($role, $roles, true)) {
    fwrite(STDERR, "Rol no válido. Use uno de: " . implode(', ', $roles) . "\n");
    exit(1);
}

$user = \App\Models\User::where('email', $email)->first();
if ($user) {
    fwrite(STDERR, "Ya existe un usuario con el email: {$email}\n");
    exit(1);
}

\App\Models\User::create([
    'name'     => $name,
    'email'    => $email,
    'password' => \Illuminate\Support\Facades\Hash::make($password),
    'role'     => $role,
]);

echo "Usuario creado: {$email} (rol: {$role})\n";
exit(0);
