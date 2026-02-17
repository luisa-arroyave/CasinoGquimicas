<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_cobro', function (Blueprint $table) {
            $table->unsignedInteger('id_cuenta', true)->primary();
            $table->unsignedInteger('id_casino');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedInteger('total_vales')->nullable();
            $table->decimal('valor_total', 12, 2)->nullable();
            $table->string('archivo_pdf', 255)->nullable();
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->timestamps();

            $table->foreign('id_casino')->references('id_casino')->on('casinos')->cascadeOnDelete();
            $table->index(['id_casino', 'fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_cobro');
    }
};
