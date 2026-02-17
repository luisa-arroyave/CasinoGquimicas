<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->string('tipo_pedido', 20)->default('en_sitio')->after('estado');
            $table->string('direccion_entrega', 500)->nullable()->after('tipo_pedido');
        });
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->dropColumn(['tipo_pedido', 'direccion_entrega']);
        });
    }
};
