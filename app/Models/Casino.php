<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Casino extends Model
{
    use SoftDeletes;

    protected $table = 'casinos';

    protected $primaryKey = 'id_casino';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'NIT',
        'nombre',
        'id_empresa',
        'tipo_casino',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Empresa a la que pertenece el casino.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Registros de consumo en este casino.
     */
    public function registroConsumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_casino', 'id_casino');
    }

    /**
     * Cuentas de cobro del casino.
     */
    public function cuentasCobro(): HasMany
    {
        return $this->hasMany(CuentaCobro::class, 'id_casino', 'id_casino');
    }

    /**
     * Precios configurados para este casino (opcional).
     */
    public function precios(): HasMany
    {
        return $this->hasMany(Precio::class, 'id_casino', 'id_casino');
    }
}
