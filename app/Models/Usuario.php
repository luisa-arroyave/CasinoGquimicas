<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
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
        'id_casino_asignado',
        'id_sede_principal',
        'id_rol',
        'id_tipo_usuario',
        'codigo_qr',
        'activo',
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
    ];

    /**
     * Empresa del usuario (empleado pertenece a una empresa).
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Casino asignado (solo para usuarios con rol casino). Null para el resto.
     */
    public function casinoAsignado(): BelongsTo
    {
        return $this->belongsTo(Casino::class, 'id_casino_asignado', 'id_casino');
    }

    /**
     * Sede donde el usuario suele estar (empleados: para mostrar casinos de esa sede).
     */
    public function sedePrincipal(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'id_sede_principal', 'id_sede');
    }

    /**
     * Sedes adicionales donde puede registrar consumo (p. ej. externos en varias oficinas).
     */
    public function sedesAcceso(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'sede_usuario', 'id_usuario', 'id_sede');
    }

    /**
     * IDs de sedes donde puede solicitar consumo: sede principal + sedes de acceso.
     */
    public function getSedesPermitidasIdsAttribute(): array
    {
        $ids = [];
        if ($this->id_sede_principal) {
            $ids[] = $this->id_sede_principal;
        }
        // Usar columna calificada para evitar ambigüedad entre sedes.id_sede y sede_usuario.id_sede
        $ids = array_merge($ids, $this->sedesAcceso()->pluck('sedes.id_sede')->toArray());
        return array_values(array_unique($ids));
    }

    /**
     * Empresas cuyos registros de consumo puede ver (solo para rol administrador o gestionhumana).
     * Select explícito para evitar ambigüedad de id_empresa entre empresas y empresa_usuario.
     */
    public function empresasAcceso(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_usuario', 'id_usuario', 'id_empresa')
            ->select('empresas.*');
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
     * Nombre del usuario (para compatibilidad con vistas que usan auth()->user()->name).
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['nombres'] ?? '';
    }

    /**
     * Rol del usuario por nombre (para middleware y vistas que usan auth()->user()->role).
     */
    public function getRoleAttribute(): ?string
    {
        return $this->rol?->nombre;
    }

    /**
     * Comprueba si tiene un rol por nombre.
     */
    public function hasRol(string $nombre): bool
    {
        return $this->rol && $this->rol->nombre === $nombre;
    }

    /**
     * Comprueba si tiene alguno de los roles indicados (para @role y middleware).
     *
     * @param  array<string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        $nombreRol = $this->rol?->nombre;
        return $nombreRol !== null && in_array($nombreRol, $roles, true);
    }

    /**
     * Identificador usado por la sesión de autenticación.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id_usuario';
    }

    /**
     * Campo de contraseña que usará Laravel para autenticación.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash ?? '';
    }
}
