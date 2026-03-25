<?php

namespace App\Models;

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
        'casino_nombre',
        'horario_nombre',
        'tipo_comida',
        'fecha_consumo',
        'hora_consumo',
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
        'precio_casino' => 'decimal:2',
        'precio_empleado' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

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
