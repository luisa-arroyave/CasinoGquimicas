<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Usuarios del sistema pueden tener acceso a varias empresas (informes, empleados).
     */
    public function up(): void
    {
        Schema::create('empresa_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('id_empresa');
            $table->primary(['user_id', 'id_empresa']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_user');
    }
};
