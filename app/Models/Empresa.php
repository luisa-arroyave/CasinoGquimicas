<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes;

    protected $table = 'empresas';

    protected $primaryKey = 'id_empresa';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'NIT',
        'nombre',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * Usuarios de la empresa.
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Casinos/restaurantes de la empresa.
     */
    public function casinos(): HasMany
    {
        return $this->hasMany(Casino::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Registros de consumo de la empresa.
     */
    public function registroConsumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Usuarios del sistema con acceso a esta empresa (informes, empleados).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'empresa_user', 'id_empresa', 'user_id');
    }
}
