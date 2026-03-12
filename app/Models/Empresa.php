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
        'correos_cuenta_cobro',
    ];

    /**
     * Lista de correos para soporte de factura (parseados desde correos_cuenta_cobro).
     *
     * @return array<int, string>
     */
    public function getCorreosCuentaCobroListAttribute(): array
    {
        $text = $this->correos_cuenta_cobro ?? '';
        if (trim($text) === '') {
            return [];
        }
        $emails = preg_split('/[\s,;]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter(array_map('trim', $emails), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    }

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
     * Casinos que atienden a esta empresa (cuyos empleados pueden consumir ahí).
     */
    public function casinos(): BelongsToMany
    {
        return $this->belongsToMany(Casino::class, 'casino_empresa', 'id_empresa', 'id_casino');
    }

    /**
     * Registros de consumo de la empresa.
     */
    public function registroConsumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Usuarios con acceso a ver registros de consumo de esta empresa (administrador / gestión humana).
     */
    public function usuariosConAcceso(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'empresa_usuario', 'id_empresa', 'id_usuario');
    }
}
