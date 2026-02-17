<?php

namespace App\Http\Controllers;

use App\Mail\CuentaCobroEnviada;
use App\Models\Casino;
use App\Models\CuentaCobro;
use App\Models\RegistroConsumo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CuentaCobroController extends Controller
{
    /**
     * Formulario: seleccionar casino y rango de fechas para generar cuenta de cobro.
     */
    public function index(Request $request): View
    {
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();
        $cuentaGenerada = null;
        if ($request->has('cuenta_id')) {
            $cuentaGenerada = CuentaCobro::with('casino')->find($request->input('cuenta_id'));
        }

        return view('casino.cuenta-cobro.index', [
            'casinos' => $casinos,
            'cuentaGenerada' => $cuentaGenerada,
        ]);
    }

    /**
     * Generar informe: calcular totales, guardar en cuentas_cobro, generar PDF.
     */
    public function generar(Request $request)
    {
        $request->validate([
            'id_casino' => ['required', 'integer', 'exists:casinos,id_casino'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ], [
            'id_casino.required' => 'Seleccione un casino.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha fin es obligatoria.',
        ]);

        $idCasino = (int) $request->input('id_casino');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $casino = Casino::findOrFail($idCasino);

        $consumos = RegistroConsumo::query()
            ->where('id_casino', $idCasino)
            ->whereBetween('fecha_consumo', [$fechaInicio, $fechaFin])
            ->where('estado', 'ENTREGADO')
            ->with(['usuario', 'visitante', 'horarioConsumo', 'empresa'])
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo')
            ->get();

        $totalAlmuerzos = $consumos->count();
        $valorTotal = round($consumos->sum('precio_casino'), 2);

        $cuenta = CuentaCobro::create([
            'id_casino' => $idCasino,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'total_vales' => $totalAlmuerzos,
            'valor_total' => $valorTotal,
            'archivo_pdf' => null,
        ]);

        $nombreArchivo = 'cuenta-cobro-' . $cuenta->id_cuenta . '-' . $fechaInicio . '-' . $fechaFin . '.pdf';
        $rutaPdf = 'cuentas_cobro/' . $nombreArchivo;

        $cuenta->load('casino');
        $pdf = Pdf::loadView('casino.cuenta-cobro.pdf', [
            'cuenta' => $cuenta,
            'casino' => $casino,
            'consumos' => $consumos,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'total_almuerzos' => $totalAlmuerzos,
            'valor_total' => $valorTotal,
        ]);

        Storage::disk('local')->put($rutaPdf, $pdf->output());
        $cuenta->update(['archivo_pdf' => $rutaPdf]);

        return redirect()->route('casino.cuenta-cobro.index', ['cuenta_id' => $cuenta->id_cuenta])
            ->with('success', 'Cuenta de cobro generada. Puede descargar el PDF o enviarlo a contabilidad.');
    }

    /**
     * Descargar PDF de la cuenta de cobro.
     */
    public function descargar(int $id)
    {
        $cuenta = CuentaCobro::with('casino')->findOrFail($id);
        if (! $cuenta->archivo_pdf || ! Storage::disk('local')->exists($cuenta->archivo_pdf)) {
            return redirect()->route('casino.cuenta-cobro.index')->with('error', 'El archivo PDF no está disponible.');
        }

        $nombreDescarga = 'cuenta-cobro-' . $cuenta->casino->nombre . '-' . $cuenta->fecha_inicio->format('Y-m-d') . '.pdf';
        $nombreDescarga = preg_replace('/[^a-zA-Z0-9\-_.]/', '-', $nombreDescarga);

        return response()->download(Storage::disk('local')->path($cuenta->archivo_pdf), $nombreDescarga, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Enviar PDF por correo a contabilidad.
     */
    public function enviarCorreo(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $cuenta = CuentaCobro::with('casino')->findOrFail($id);
        if (! $cuenta->archivo_pdf || ! Storage::disk('local')->exists($cuenta->archivo_pdf)) {
            return redirect()->route('casino.cuenta-cobro.index')->with('error', 'El archivo PDF no está disponible.');
        }

        $emailContabilidad = $request->input('email_contabilidad', config('mail.contabilidad', config('mail.from.address')));

        Mail::to($emailContabilidad)->send(new CuentaCobroEnviada($cuenta));

        return redirect()->route('casino.cuenta-cobro.index')->with('success', 'Cuenta de cobro enviada por correo a ' . $emailContabilidad);
    }
}
