<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaVisita extends Model
{
    protected $table = 'area_visita';

    protected $primaryKey = 'id_area_visita';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function registroConsumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_area_visita', 'id_area_visita');
    }
}
