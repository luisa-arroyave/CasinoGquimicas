<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_consumos', function (Blueprint $table) {
            $table->unsignedInteger('id_consumo', true)->primary();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->unsignedInteger('id_visitante')->nullable();
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('id_casino');
            $table->unsignedInteger('id_horario');
            $table->date('fecha_consumo');
            $table->time('hora_consumo');
            $table->decimal('precio_casino', 10, 2);
            $table->decimal('precio_empleado', 10, 2);
            $table->unsignedInteger('registrado_por')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('estado', 20);
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->nullOnDelete();
            $table->foreign('id_visitante')->references('id_visitante')->on('visitantes')->nullOnDelete();
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->cascadeOnDelete();
            $table->foreign('id_casino')->references('id_casino')->on('casinos')->cascadeOnDelete();
            $table->foreign('id_horario')->references('id_horario')->on('horarios_consumo')->cascadeOnDelete();
            $table->foreign('registrado_por')->references('id_usuario')->on('usuarios')->nullOnDelete();

            // Evita doble consumo por horario (usuario + fecha + horario)
            $table->unique(['id_usuario', 'fecha_consumo', 'id_horario'], 'uk_consumo_usuario');
            $table->index('fecha_consumo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_consumos');
    }
};
