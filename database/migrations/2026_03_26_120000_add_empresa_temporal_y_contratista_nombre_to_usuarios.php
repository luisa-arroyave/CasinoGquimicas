<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (! Schema::hasColumn('usuarios', 'empresa_temporal_nombre')) {
                $table->string('empresa_temporal_nombre', 255)->nullable()->after('id_empresa_temporal');
            }
            if (! Schema::hasColumn('usuarios', 'empresa_contratista_nombre')) {
                $table->string('empresa_contratista_nombre', 255)->nullable()->after('id_empresa_contratista');
            }
        });

        if (Schema::hasColumn('usuarios', 'empresa_temporal_nombre')) {
            DB::statement('
                UPDATE usuarios u
                LEFT JOIN empresas_temporales et ON u.id_empresa_temporal = et.id_empresa_temporal
                SET u.empresa_temporal_nombre = et.nombre
                WHERE u.id_empresa_temporal IS NOT NULL
            ');
        }

        if (Schema::hasColumn('usuarios', 'empresa_contratista_nombre')) {
            DB::statement('
                UPDATE usuarios u
                LEFT JOIN empresas_contratistas ec ON u.id_empresa_contratista = ec.id_empresa_contratista
                SET u.empresa_contratista_nombre = ec.nombre
                WHERE u.id_empresa_contratista IS NOT NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'empresa_contratista_nombre')) {
                $table->dropColumn('empresa_contratista_nombre');
            }
        });
    }
};
