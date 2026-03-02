<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id_casino_asignado')->nullable()->after('id_empresa');
            $table->foreign('id_casino_asignado')
                ->references('id_casino')
                ->on('casinos')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_casino_asignado']);
            $table->dropColumn('id_casino_asignado');
        });
    }
};
