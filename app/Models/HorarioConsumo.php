<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HorarioConsumo extends Model
{
    use SoftDeletes;

    protected $table = 'horarios_consumo';

    protected $primaryKey = 'id_horario';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Registros de consumo en este horario.
     */
    public function registroConsumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_horario', 'id_horario');
    }

    /**
     * Precios configurados para este horario.
     */
    public function precios(): HasMany
    {
        return $this->hasMany(Precio::class, 'id_horario', 'id_horario');
    }

    /**
     * Obtener el horario de consumo vigente según la hora actual.
     */
    public static function horarioVigente(): ?self
    {
        return static::query()
            ->where('activo', true)
            ->whereRaw('CURTIME() BETWEEN hora_inicio AND hora_fin')
            ->first();
    }
}
