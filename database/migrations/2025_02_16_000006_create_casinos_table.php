<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casinos', function (Blueprint $table) {
            $table->unsignedInteger('id_casino', true)->primary();
            $table->string('NIT', 30)->unique();
            $table->string('nombre', 120);
            $table->unsignedInteger('id_empresa');
            $table->string('tipo_casino', 20);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->cascadeOnDelete();
            $table->index(['activo', 'id_empresa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casinos');
    }
};
