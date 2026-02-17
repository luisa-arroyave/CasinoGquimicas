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
        'id_empresa',
        'id_casino',
        'id_horario',
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
}
