<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaCobro extends Model
{
    protected $table = 'cuentas_cobro';

    protected $primaryKey = 'id_cuenta';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_casino',
        'fecha_inicio',
        'fecha_fin',
        'total_vales',
        'valor_total',
        'archivo_pdf',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor_total' => 'decimal:2',
        'fecha_generacion' => 'datetime',
    ];

    /**
     * Casino de la cuenta de cobro.
     */
    public function casino(): BelongsTo
    {
        return $this->belongsTo(Casino::class, 'id_casino', 'id_casino');
    }
}
