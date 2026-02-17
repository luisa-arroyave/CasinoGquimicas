<?php

namespace App\Http\Controllers;

use App\Models\RegistroConsumo;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperativoPedidosController extends Controller
{
    /**
     * Listar pedidos a domicilio pendientes (estado distinto de ENTREGADO).
     */
    public function index(Request $request): View
    {
        $query = RegistroConsumo::with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->where('tipo_pedido', 'domicilio')
            ->where('estado', '!=', 'ENTREGADO');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_consumo', $request->fecha);
        } else {
            $query->whereDate('fecha_consumo', '>=', Carbon::today());
        }

        $pedidos = $query->orderBy('fecha_consumo')->orderBy('hora_consumo')->paginate(20)->withQueryString();

        return view('operativo.pedidos-domicilio.index', compact('pedidos'));
    }

    /**
     * Marcar pedido a domicilio como ENTREGADO.
     */
    public function marcarEntregado(Request $request, RegistroConsumo $consumo): RedirectResponse
    {
        if ($consumo->tipo_pedido !== 'domicilio') {
            return redirect()->route('operativo.pedidos.index')->with('error', 'Solo se puede marcar como entregado un pedido a domicilio.');
        }

        if ($consumo->estado === 'ENTREGADO') {
            return redirect()->route('operativo.pedidos.index')->with('success', 'El pedido ya estaba marcado como entregado.');
        }

        $consumo->update([
            'estado' => 'ENTREGADO',
            'hora_consumo' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect()->route('operativo.pedidos.index')->with('success', 'Pedido marcado como ENTREGADO.');
    }
}
