<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->string('empresa_temporal_nombre', 255)->nullable()->after('empresa_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->dropColumn('empresa_temporal_nombre');
        });
    }
};
