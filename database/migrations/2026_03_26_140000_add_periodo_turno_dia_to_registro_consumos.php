<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('registro_consumos', 'periodo_turno_dia')) {
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->unsignedTinyInteger('periodo_turno_dia')->default(1)->after('hora_consumo');
            });
        }

        $corte = (string) env('CONSUMO_PERIODO_TURNO_HORA_CORTE', '12:00');
        if (! preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $corte)) {
            $corte = '12:00:00';
        } elseif (strlen($corte) === 5) {
            $corte .= ':00';
        }

        DB::statement('UPDATE registro_consumos SET periodo_turno_dia = IF(TIME(hora_consumo) < TIME(?), 1, 2)', [$corte]);

        // La FK id_usuario reutiliza uk_consumo_usuario como índice; hay que añadir otro índice en id_usuario antes de quitar el unique.
        if (Schema::hasIndex('registro_consumos', 'uk_consumo_usuario')) {
            if (! Schema::hasIndex('registro_consumos', 'idx_registro_consumos_id_usuario')) {
                Schema::table('registro_consumos', function (Blueprint $table) {
                    $table->index('id_usuario', 'idx_registro_consumos_id_usuario');
                });
            }
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->dropUnique('uk_consumo_usuario');
            });
        }

        if (! Schema::hasIndex('registro_consumos', 'uk_consumo_usuario_periodo')) {
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->unique(
                    ['id_usuario', 'fecha_consumo', 'id_horario', 'periodo_turno_dia'],
                    'uk_consumo_usuario_periodo'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('registro_consumos', 'uk_consumo_usuario_periodo')) {
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->dropUnique('uk_consumo_usuario_periodo');
            });
        }

        if (! Schema::hasIndex('registro_consumos', 'uk_consumo_usuario')) {
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->unique(['id_usuario', 'fecha_consumo', 'id_horario'], 'uk_consumo_usuario');
            });
        }

        if (Schema::hasIndex('registro_consumos', 'idx_registro_consumos_id_usuario')) {
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->dropIndex('idx_registro_consumos_id_usuario');
            });
        }

        if (Schema::hasColumn('registro_consumos', 'periodo_turno_dia')) {
            Schema::table('registro_consumos', function (Blueprint $table) {
                $table->dropColumn('periodo_turno_dia');
            });
        }
    }
};
