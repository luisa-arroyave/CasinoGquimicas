<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use Illuminate\View\View;

class CasinoEscaneoController extends Controller
{
    /**
     * Vista de escaneo QR para entrega de consumos (casino/operativo).
     */
    public function index(): View
    {
        $query = Casino::where('activo', true);
        $user = auth()->user();
        if ($user && $user->role === 'casino' && $user->id_casino_asignado) {
            $query->where('id_casino', $user->id_casino_asignado);
        }
        $casinos = $query->orderBy('nombre')->get();

        return view('casino.escaneo-qr', [
            'casinos' => $casinos,
        ]);
    }
}
