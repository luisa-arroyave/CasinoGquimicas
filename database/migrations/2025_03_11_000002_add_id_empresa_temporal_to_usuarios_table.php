<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id_empresa_temporal')->nullable()->after('id_tipo_usuario');
            $table->foreign('id_empresa_temporal')->references('id_empresa_temporal')->on('empresas_temporales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_empresa_temporal']);
        });
    }
};
