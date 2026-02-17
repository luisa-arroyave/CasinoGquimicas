<?php

namespace Database\Seeders;

use App\Models\HorarioConsumo;
use Illuminate\Database\Seeder;

class HorarioConsumoSeeder extends Seeder
{
    /**
     * Horarios de consumo por defecto.
     */
    public function run(): void
    {
        $horarios = [
            ['nombre' => 'REFRIGERIO', 'hora_inicio' => '00:00:00', 'hora_fin' => '11:29:59'],
            ['nombre' => 'ALMUERZO', 'hora_inicio' => '11:30:00', 'hora_fin' => '17:00:00'],
            ['nombre' => 'CENA', 'hora_inicio' => '17:01:00', 'hora_fin' => '23:59:59'],
        ];

        foreach ($horarios as $h) {
            HorarioConsumo::firstOrCreate(
                ['nombre' => $h['nombre']],
                [
                    'nombre' => $h['nombre'],
                    'hora_inicio' => $h['hora_inicio'],
                    'hora_fin' => $h['hora_fin'],
                    'activo' => true,
                ]
            );
        }
    }
}
