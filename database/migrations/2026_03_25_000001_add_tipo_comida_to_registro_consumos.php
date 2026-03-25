<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->string('tipo_comida', 50)->nullable()->after('horario_nombre');
        });

        DB::statement('UPDATE registro_consumos SET tipo_comida = horario_nombre WHERE tipo_comida IS NULL AND horario_nombre IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->dropColumn('tipo_comida');
        });
    }
};
