<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'documento',
        'nombres',
        'email',
        'password_hash',
        'id_empresa',
        'id_rol',
        'id_tipo_usuario',
        'codigo_qr',
        'activo',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
    ];

    /**
     * Empresa del usuario.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Rol del usuario.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    /**
     * Tipo de usuario (FIJO, TEMPORAL, SENA, etc.).
     */
    public function tipoUsuario(): BelongsTo
    {
        return $this->belongsTo(TipoUsuario::class, 'id_tipo_usuario', 'id_tipo_usuario');
    }

    /**
     * Consumos registrados por este usuario.
     */
    public function consumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Consumos que este usuario registró (como operativo).
     */
    public function consumosRegistrados(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'registrado_por', 'id_usuario');
    }

    /**
     * Comprueba si tiene un rol por nombre.
     */
    public function hasRol(string $nombre): bool
    {
        return $this->rol && $this->rol->nombre === $nombre;
    }
}
