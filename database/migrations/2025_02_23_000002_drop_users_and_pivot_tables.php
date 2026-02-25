<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // El sistema de autenticación ahora usa solo la tabla "usuarios".
        // Podemos eliminar las tablas antiguas relacionadas con "users".
        if (Schema::hasTable('empresa_user')) {
            Schema::drop('empresa_user');
        }

        if (Schema::hasTable('users')) {
            Schema::drop('users');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se recrean las tablas eliminadas.
    }
};

