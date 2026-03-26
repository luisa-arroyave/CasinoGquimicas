<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroConsumo extends Model
{
    protected $table = 'registro_consumos';

    protected $primaryKey = 'id_consumo';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_usuario',
        'id_visitante',
        'id_area_visita',
        'id_empresa',
        'id_casino',
        'id_horario',
        'documento',
        'nombres_consumidor',
        'tipo_usuario_nombre',
        'empresa_nombre',
        'empresa_temporal_nombre',
        'empresa_contratista_nombre',
        'casino_nombre',
        'horario_nombre',
        'tipo_comida',
        'fecha_consumo',
        'hora_consumo',
        'periodo_turno_dia',
        'precio_casino',
        'precio_empleado',
        'registrado_por',
        'estado',
        'tipo_pedido',
        'direccion_entrega',
    ];

    protected $casts = [
        'fecha_consumo' => 'date',
        'hora_consumo' => 'datetime:H:i',
        'periodo_turno_dia' => 'integer',
        'precio_casino' => 'decimal:2',
        'precio_empleado' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (RegistroConsumo $r) {
            try {
                $fecha = $r->fecha_consumo instanceof Carbon
                    ? $r->fecha_consumo->format('Y-m-d')
                    : ($r->getAttributes()['fecha_consumo'] ?? null);
                if (! $fecha) {
                    return;
                }
                $horaRaw = $r->getRawOriginal('hora_consumo') ?? $r->getAttributes()['hora_consumo'] ?? null;
                if ($horaRaw instanceof \DateTimeInterface) {
                    $horaRaw = Carbon::instance($horaRaw)->format('H:i:s');
                }
                if ($horaRaw === null || $horaRaw === '') {
                    return;
                }
                $r->periodo_turno_dia = static::periodoTurnoDiaDesdeFechaHora((string) $fecha, (string) $horaRaw);
            } catch (\Throwable) {
                //
            }
        });
    }

    /**
     * 1 = antes de la hora de corte del día; 2 = desde la hora de corte (turnos noche/misma fecha).
     */
    public static function periodoTurnoDiaDesdeFechaHora(string $fechaYmd, mixed $horaMx): int
    {
        $cut = (string) config('consumo.periodo_turno_hora_corte', '12:00');
        if (! preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $cut)) {
            $cut = '12:00:00';
        } elseif (strlen($cut) === 5) {
            $cut .= ':00';
        }

        if ($horaMx instanceof \DateTimeInterface) {
            $horaStr = Carbon::instance($horaMx)->format('H:i:s');
        } else {
            $horaStr = trim((string) $horaMx);
            if ($horaStr === '') {
                return 1;
            }
            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $horaStr)) {
                $horaStr = strlen($horaStr) === 5 ? $horaStr . ':00' : $horaStr;
            } else {
                $horaStr = Carbon::parse($horaStr)->format('H:i:s');
            }
        }

        $dt = Carbon::parse($fechaYmd . ' ' . $horaStr);
        $corte = Carbon::parse($fechaYmd . ' ' . $cut);

        return $dt->lt($corte) ? 1 : 2;
    }

    /**
     * Usuario que consumió (empleado); null si es visitante.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Visitante que consumió; null si es empleado.
     */
    public function visitante(): BelongsTo
    {
        return $this->belongsTo(Visitante::class, 'id_visitante', 'id_visitante');
    }

    /**
     * Área de visita (cuando el consumidor es visitante).
     */
    public function areaVisita(): BelongsTo
    {
        return $this->belongsTo(AreaVisita::class, 'id_area_visita', 'id_area_visita');
    }

    /**
     * Empresa asociada al consumo.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Casino donde se registró el consumo.
     */
    public function casino(): BelongsTo
    {
        return $this->belongsTo(Casino::class, 'id_casino', 'id_casino');
    }

    /**
     * Horario de consumo (REFRIGERIO, ALMUERZO, CENA).
     */
    public function horarioConsumo(): BelongsTo
    {
        return $this->belongsTo(HorarioConsumo::class, 'id_horario', 'id_horario');
    }

    /**
     * Usuario que registró el consumo (operativo).
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por', 'id_usuario');
    }

    /**
     * Nombre del consumidor (preferir snapshot histórico).
     */
    public function getDisplayConsumidorAttribute(): string
    {
        if (! empty($this->nombres_consumidor)) {
            $doc = $this->documento ? " ({$this->documento})" : '';
            return $this->nombres_consumidor . $doc;
        }
        if ($this->usuario) {
            return $this->usuario->nombres . ' (' . ($this->usuario->documento ?? '') . ')';
        }
        return ($this->visitante?->nombre ?? '-') . ' (visitante)';
    }

    /**
     * Nombre empresa (preferir snapshot).
     */
    public function getDisplayEmpresaAttribute(): ?string
    {
        return $this->empresa_nombre ?? $this->empresa?->nombre ?? '-';
    }

    /**
     * Nombre casino (preferir snapshot).
     */
    public function getDisplayCasinoAttribute(): ?string
    {
        return $this->casino_nombre ?? $this->casino?->nombre ?? '-';
    }

    /**
     * Nombre horario (preferir snapshot).
     */
    public function getDisplayHorarioAttribute(): ?string
    {
        return $this->horario_nombre ?? $this->horarioConsumo?->nombre ?? '-';
    }

    /**
     * Precio empleado formateado.
     */
    public function getDisplayPrecioEmpleadoAttribute(): string
    {
        return '$ ' . number_format((float) $this->precio_empleado, 0, ',', '.');
    }

    /**
     * Precio casino formateado.
     */
    public function getDisplayPrecioCasinoAttribute(): string
    {
        return '$ ' . number_format((float) $this->precio_casino, 0, ',', '.');
    }
}
