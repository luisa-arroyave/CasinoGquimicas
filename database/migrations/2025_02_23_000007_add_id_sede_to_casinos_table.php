<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casinos', function (Blueprint $table) {
            $table->unsignedInteger('id_sede')->nullable()->after('id_empresa');
            $table->foreign('id_sede')->references('id_sede')->on('sedes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('casinos', function (Blueprint $table) {
            $table->dropForeign(['id_sede']);
            $table->dropColumn('id_sede');
        });
    }
};
