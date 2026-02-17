<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Roles del sistema según estructura empresarial.
     */
    public function run(): void
    {
        $roles = [
            'empleado',
            'casino',
            'gestionhumana',
            'administrador',
            'operativo',
        ];

        foreach ($roles as $nombre) {
            Role::firstOrCreate(
                ['nombre' => $nombre],
                ['nombre' => $nombre]
            );
        }
    }
}
