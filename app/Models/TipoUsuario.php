<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoUsuario extends Model
{
    protected $table = 'tipos_usuario';

    protected $primaryKey = 'id_tipo_usuario';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['nombre'];

    /**
     * Usuarios de este tipo.
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_tipo_usuario', 'id_tipo_usuario');
    }
}
