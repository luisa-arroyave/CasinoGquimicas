<?php

namespace Database\Seeders;

use App\Models\TipoUsuario;
use Illuminate\Database\Seeder;

class TipoUsuarioSeeder extends Seeder
{
    /**
     * Tipos de usuario (empleados).
     */
    public function run(): void
    {
        $tipos = [
            'FIJO',
            'TEMPORAL',
            'SENA',
            'PASANTE',
            'CONTRATISTA',
            'INVITADO',
        ];

        foreach ($tipos as $nombre) {
            TipoUsuario::firstOrCreate(
                ['nombre' => $nombre],
                ['nombre' => $nombre]
            );
        }
    }
}
