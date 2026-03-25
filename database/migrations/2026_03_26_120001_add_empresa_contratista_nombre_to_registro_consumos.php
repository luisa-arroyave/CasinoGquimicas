<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            if (! Schema::hasColumn('registro_consumos', 'empresa_contratista_nombre')) {
                $table->string('empresa_contratista_nombre', 255)->nullable()->after('empresa_temporal_nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            if (Schema::hasColumn('registro_consumos', 'empresa_contratista_nombre')) {
                $table->dropColumn('empresa_contratista_nombre');
            }
        });
    }
};
