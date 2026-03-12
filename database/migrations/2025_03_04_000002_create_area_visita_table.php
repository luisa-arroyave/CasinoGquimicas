<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_visita', function (Blueprint $table) {
            $table->unsignedInteger('id_area_visita', true)->primary();
            $table->string('nombre', 100);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar áreas por defecto
        DB::table('area_visita')->insert([
            ['nombre' => 'Reunión', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Auditoría', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Proveedor', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Capacitación', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('area_visita');
    }
};
