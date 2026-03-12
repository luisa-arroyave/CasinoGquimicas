<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmpresaTemporal extends Model
{
    protected $table = 'empresas_temporales';

    protected $primaryKey = 'id_empresa_temporal';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['nombre', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_empresa_temporal', 'id_empresa_temporal');
    }
}
