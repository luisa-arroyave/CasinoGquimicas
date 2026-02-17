<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitante extends Model
{
    use SoftDeletes;

    protected $table = 'visitantes';

    protected $primaryKey = 'id_visitante';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'documento',
        'empresa_visita',
        'area_visita',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Consumos del visitante.
     */
    public function consumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_visitante', 'id_visitante');
    }
}
