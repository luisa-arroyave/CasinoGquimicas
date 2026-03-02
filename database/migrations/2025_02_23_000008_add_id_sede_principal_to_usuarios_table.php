<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id_sede_principal')->nullable()->after('id_casino_asignado');
            $table->foreign('id_sede_principal')->references('id_sede')->on('sedes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_sede_principal']);
            $table->dropColumn('id_sede_principal');
        });
    }
};
