<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->dropColumn(['precio_casino_texto', 'precio_empleado_texto']);
        });
    }

    public function down(): void
    {
        Schema::table('registro_consumos', function (Blueprint $table) {
            $table->string('precio_casino_texto', 50)->nullable()->after('precio_casino');
            $table->string('precio_empleado_texto', 50)->nullable()->after('precio_empleado');
        });
    }
};
