<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade columnas snapshot para conservar datos históricos
     * aunque se modifiquen usuarios, empresas o precios.
     */
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->string('documento', 50)->nullable()->after('id_visitante');
            $table->string('nombres_consumidor', 255)->nullable()->after('documento');
            $table->string('tipo_usuario_nombre', 50)->nullable()->after('nombres_consumidor');
            $table->string('empresa_nombre', 255)->nullable()->after('tipo_usuario_nombre');
            $table->string('casino_nombre', 255)->nullable()->after('id_casino');
            $table->string('horario_nombre', 100)->nullable()->after('id_horario');
            $table->string('precio_casino_texto', 50)->nullable()->after('precio_casino');
            $table->string('precio_empleado_texto', 50)->nullable()->after('precio_empleado');
        });
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->dropColumn([
                'documento',
                'nombres_consumidor',
                'tipo_usuario_nombre',
                'empresa_nombre',
                'casino_nombre',
                'horario_nombre',
                'precio_casino_texto',
                'precio_empleado_texto',
            ]);
        });
    }
};
