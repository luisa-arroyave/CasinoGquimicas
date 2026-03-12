<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->unsignedInteger('id_area_visita')->nullable()->after('id_visitante');
            $table->foreign('id_area_visita')->references('id_area_visita')->on('area_visita')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->dropForeign(['id_area_visita']);
            $table->dropColumn('id_area_visita');
        });
    }
};
