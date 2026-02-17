<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precios', function (Blueprint $table) {
            $table->unsignedInteger('id_precio', true)->primary();
            $table->unsignedInteger('id_horario');
            $table->unsignedInteger('id_casino')->nullable();
            $table->decimal('precio_empleado', 10, 2)->default(0);
            $table->decimal('precio_casino', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_horario')->references('id_horario')->on('horarios_consumo')->cascadeOnDelete();
            $table->foreign('id_casino')->references('id_casino')->on('casinos')->cascadeOnDelete();
            $table->unique(['id_horario', 'id_casino']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
