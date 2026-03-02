<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Correos a los que se envía la cuenta de cobro cuando el casino la envía a contabilidad.
     * Se pueden indicar varios separados por coma, punto y coma o espacio.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->text('correos_cuenta_cobro')->nullable()->after('activa');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('correos_cuenta_cobro');
        });
    }
};
