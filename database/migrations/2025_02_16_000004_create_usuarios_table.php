<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id_usuario', true)->primary();
            $table->string('documento', 20)->unique();
            $table->string('nombres', 80);
            $table->string('email', 120)->nullable();
            $table->string('password_hash', 255)->nullable();
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_tipo_usuario')->nullable();
            $table->string('codigo_qr', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->cascadeOnDelete();
            $table->foreign('id_rol')->references('id_rol')->on('roles')->cascadeOnDelete();
            $table->foreign('id_tipo_usuario')->references('id_tipo_usuario')->on('tipos_usuario')->nullOnDelete();
            $table->index(['activo', 'id_empresa']);
            $table->index('id_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
