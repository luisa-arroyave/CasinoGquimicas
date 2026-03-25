<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas_contratistas', function (Blueprint $table) {
            $table->unsignedInteger('id_empresa_contratista', true)->primary();
            $table->string('nit', 30)->unique();
            $table->string('nombre', 255);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas_contratistas');
    }
};
