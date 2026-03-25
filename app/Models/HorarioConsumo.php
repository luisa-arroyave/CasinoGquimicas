<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class HorarioConsumo extends Model
{
    use SoftDeletes;

    protected $table = 'horarios_consumo';

    protected $primaryKey = 'id_horario';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Registros de consumo en este horario.
     */
    public function registroConsumos(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_horario', 'id_horario');
    }

    /**
     * Precios configurados para este horario.
     */
    public function precios(): HasMany
    {
        return $this->hasMany(Precio::class, 'id_horario', 'id_horario');
    }

    /**
     * SQL: CURTIME() cae dentro de [hora_inicio, hora_fin].
     * Si hora_inicio > hora_fin, el turno cruza medianoche (ej. 22:00 a 06:00).
     */
    public static function sqlCurtimeDentroDeRangoHorario(): string
    {
        return '(hora_inicio <= hora_fin AND CURTIME() BETWEEN hora_inicio AND hora_fin) OR (hora_inicio > hora_fin AND (CURTIME() >= hora_inicio OR CURTIME() <= hora_fin))';
    }

    /**
     * Obtener el horario de consumo vigente según la hora actual (incluye turnos nocturnos).
     */
    public static function horarioVigente(): ?self
    {
        return static::query()
            ->where('activo', true)
            ->whereRaw('(' . static::sqlCurtimeDentroDeRangoHorario() . ')')
            ->orderBy('hora_inicio')
            ->orderBy('id_horario')
            ->first();
    }

    /**
     * Ventana donde refrigerio y cena comparten el mismo rango en BD: desde 15:01 hasta 11:29 (cruza medianoche en lógica de negocio).
     * Excluye 11:30–15:00 (p. ej. almuerzo u otros horarios).
     */
    public static function enVentanaRefrigerioCena(?Carbon $ahora = null): bool
    {
        $ahora = $ahora ?? Carbon::now();
        $minutes = $ahora->hour * 60 + $ahora->minute;

        return ($minutes >= 15 * 60 + 1) || ($minutes <= 11 * 60 + 29);
    }

    /**
     * Horarios activos REFRIGERIO o CENA que cubren la hora actual (mismo rango configurado en BD).
     *
     * @return Collection<int, HorarioConsumo>
     */
    public static function horariosRefrigerioCenaCubrenAhora(): Collection
    {
        if (! static::enVentanaRefrigerioCena()) {
            return collect();
        }

        return static::query()
            ->where('activo', true)
            ->whereRaw('(' . static::sqlCurtimeDentroDeRangoHorario() . ')')
            ->orderBy('hora_inicio')
            ->get()
            ->filter(function (self $h) {
                $n = mb_strtoupper(trim((string) $h->nombre), 'UTF-8');

                return in_array($n, ['REFRIGERIO', 'CENA'], true);
            })
            ->values();
    }

    /**
     * Horario de referencia para pantalla y consumo por defecto (no IBC en ventana): prioriza CENA si existe en la ventana.
     */
    public static function horarioParaSolicitudEmpleado(): ?self
    {
        if (static::enVentanaRefrigerioCena()) {
            $rc = static::horariosRefrigerioCenaCubrenAhora();
            if ($rc->isNotEmpty()) {
                return $rc->first(fn (self $h) => mb_strtoupper(trim((string) $h->nombre), 'UTF-8') === 'CENA') ?? $rc->first();
            }
        }

        return static::horarioVigente();
    }

    /**
     * IDs de horario que comparten el mismo “cupo” del día para refrigerio/cena solapados (un solo solicitud en la ventana).
     *
     * @return array<int>
     */
    public static function idsHorariosSlotConsumoActual(): array
    {
        if (static::enVentanaRefrigerioCena()) {
            $rc = static::horariosRefrigerioCenaCubrenAhora();
            if ($rc->isNotEmpty()) {
                return $rc->pluck('id_horario')->unique()->values()->all();
            }
        }

        $h = static::horarioVigente();

        return $h ? [$h->id_horario] : [];
    }

    /**
     * Horario efectivo al guardar solicitud de consumo (empleado): IBC elige en ventana; resto CENA por defecto en esa ventana.
     *
     * @param  array<string, mixed>  $valid  Debe incluir id_horario si aplica validación previa
     */
    public static function horarioParaRegistrarConsumoEmpleado(Usuario $usuario, array $valid): ?self
    {
        $permitidos = static::horariosRefrigerioCenaCubrenAhora();

        if (static::enVentanaRefrigerioCena() && $permitidos->isNotEmpty()) {
            if ($usuario->esEmpleadoIbc()) {
                $id = (int) ($valid['id_horario'] ?? 0);
                $horario = $permitidos->firstWhere('id_horario', $id);

                return $horario instanceof self ? $horario : null;
            }

            return $permitidos->first(fn (self $h) => mb_strtoupper(trim((string) $h->nombre), 'UTF-8') === 'CENA')
                ?? $permitidos->first();
        }

        return static::horarioVigente();
    }
}
