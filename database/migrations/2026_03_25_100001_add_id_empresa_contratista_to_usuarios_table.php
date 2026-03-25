<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id_empresa_contratista')->nullable()->after('id_empresa_temporal');
            $table->foreign('id_empresa_contratista')
                ->references('id_empresa_contratista')
                ->on('empresas_contratistas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_empresa_contratista']);
            $table->dropColumn('id_empresa_contratista');
        });
    }
};
