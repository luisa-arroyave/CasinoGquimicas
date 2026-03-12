@extends('layouts.app')

@section('title', 'Código QR - Consumo en otra sede')
@section('page-title', 'Código QR para entrega')

@section('content')
<div class="max-w-md space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <p class="text-slate-600 text-sm sm:text-base mb-4">Consumo registrado con estado <strong>Solicitado</strong>. Presente este código QR en el casino seleccionado para que lo validen.</p>

        <div class="flex flex-col items-center gap-4">
            <div class="rounded-xl border-2 border-slate-200 bg-white p-4 inline-block">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=256x256&amp;data={{ urlencode($codigoQr) }}" alt="Código QR" width="256" height="256" class="block">
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-slate-700">{{ $consumo->horarioConsumo->nombre ?? 'Consumo' }}</p>
                <p class="text-sm text-slate-500">{{ $consumo->casino->nombre ?? '' }}</p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100">
            <a href="{{ route('consumo-otra-sede.create') }}" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 rounded-xl border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 transition-colors w-full sm:w-auto">
                Volver a consumo en otra sede
            </a>
        </div>
    </div>
</div>
@endsection
