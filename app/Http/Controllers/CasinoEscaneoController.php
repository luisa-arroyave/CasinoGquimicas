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
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();

        return view('casino.escaneo-qr', [
            'casinos' => $casinos,
        ]);
    }
}
