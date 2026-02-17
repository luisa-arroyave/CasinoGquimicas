<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Precio extends Model
{
    protected $table = 'precios';

    protected $primaryKey = 'id_precio';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_horario',
        'id_casino',
        'precio_empleado',
        'precio_casino',
    ];

    protected $casts = [
        'precio_empleado' => 'decimal:2',
        'precio_casino' => 'decimal:2',
    ];

    public function horarioConsumo(): BelongsTo
    {
        return $this->belongsTo(HorarioConsumo::class, 'id_horario', 'id_horario');
    }

    public function casino(): BelongsTo
    {
        return $this->belongsTo(Casino::class, 'id_casino', 'id_casino');
    }
}
