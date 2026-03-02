<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sede_usuario', function (Blueprint $table) {
            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_sede');
            $table->primary(['id_usuario', 'id_sede']);
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnDelete();
            $table->foreign('id_sede')->references('id_sede')->on('sedes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sede_usuario');
    }
};
