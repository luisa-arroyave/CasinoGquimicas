<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casino_empresa', function (Blueprint $table) {
            $table->unsignedInteger('id_casino');
            $table->unsignedInteger('id_empresa');
            $table->primary(['id_casino', 'id_empresa']);
            $table->foreign('id_casino')->references('id_casino')->on('casinos')->cascadeOnDelete();
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->cascadeOnDelete();
        });

        // Migrar datos existentes: cada casino tenía una empresa (id_empresa)
        DB::table('casinos')->whereNotNull('id_empresa')->orderBy('id_casino')->chunk(100, function ($casinos) {
            foreach ($casinos as $c) {
                DB::table('casino_empresa')->insertOrIgnore([
                    'id_casino' => $c->id_casino,
                    'id_empresa' => $c->id_empresa,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casino_empresa');
    }
};
