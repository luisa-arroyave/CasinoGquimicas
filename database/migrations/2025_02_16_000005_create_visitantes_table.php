<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitantes', function (Blueprint $table) {
            $table->unsignedInteger('id_visitante', true)->primary();
            $table->string('nombre', 120);
            $table->string('documento', 30)->nullable();
            $table->string('empresa_visita', 120)->nullable();
            $table->string('area_visita', 120)->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitantes');
    }
};
