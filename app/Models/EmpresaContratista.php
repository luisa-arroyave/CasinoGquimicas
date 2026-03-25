<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmpresaContratista extends Model
{
    protected $table = 'empresas_contratistas';

    protected $primaryKey = 'id_empresa_contratista';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['nit', 'nombre', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_empresa_contratista', 'id_empresa_contratista');
    }
}
