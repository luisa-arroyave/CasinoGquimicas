<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sede extends Model
{
    protected $table = 'sedes';

    protected $primaryKey = 'id_sede';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['nombre'];

    /**
     * Casinos ubicados en esta sede.
     */
    public function casinos(): HasMany
    {
        return $this->hasMany(Casino::class, 'id_sede', 'id_sede');
    }

    /**
     * Usuarios que tienen esta sede como principal.
     */
    public function usuariosPrincipal(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_sede_principal', 'id_sede');
    }

    /**
     * Usuarios que pueden operar en esta sede (pivot sede_usuario).
     */
    public function usuariosAcceso(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'sede_usuario', 'id_sede', 'id_usuario');
    }
}
