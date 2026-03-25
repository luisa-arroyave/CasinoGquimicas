@extends('layouts.app')

@section('title', 'Soporte para Factura - ' . config('app.name'))
@section('page-title', 'Soporte para Factura')

@section('content')
<div class="space-y-6 max-w-2xl">
    {{-- Formulario: generar nuevo soporte para factura --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Generar soporte para factura</h2>
        <form method="post" action="{{ route('casino.soporte-factura.generar') }}" class="space-y-4">
            @csrf
            <div>
                <label for="id_casino" class="block text-sm font-medium text-slate-700 mb-1">Casino / Punto</label>
                <select name="id_casino" id="id_casino" required
                        class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500">
                    <option value="">Seleccione...</option>
                    @foreach($casinos as $c)
                        <option value="{{ $c->id_casino }}" {{ old('id_casino') == $c->id_casino ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                @error('id_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-slate-700 mb-1">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500">
                    @error('fecha_inicio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-slate-700 mb-1">Fecha fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', now()->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500">
                    @error('fecha_fin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700 focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                Generar informe PDF
            </button>
        </form>
    </div>

    {{-- Resumen y acciones tras generar --}}
    @if($cuentaGenerada)
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Soporte generado</h2>
        <div class="grid gap-4 sm:grid-cols-2 mb-6">
            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-sm font-medium text-slate-500">Total almuerzos / vales</p>
                <p class="text-2xl font-bold text-slate-800">{{ $cuentaGenerada->total_vales }}</p>
            </div>
            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-sm font-medium text-slate-500">Valor total</p>
                <p class="text-2xl font-bold text-emerald-600">$ {{ number_format($cuentaGenerada->valor_total, 0, ',', '.') }}</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-4">
            {{ $cuentaGenerada->casino->nombre }} — {{ $cuentaGenerada->fecha_inicio->format('d/m/Y') }} al {{ $cuentaGenerada->fecha_fin->format('d/m/Y') }}
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('casino.soporte-factura.descargar', $cuentaGenerada->id_cuenta) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Descargar PDF
            </a>
            @php
                $empresaCuenta = $cuentaGenerada->casino->empresa ?? null;
                $correosEmpresa = $empresaCuenta ? $empresaCuenta->correos_cuenta_cobro_list : [];
            @endphp
            @if(count($correosEmpresa) > 0)
                <p class="text-sm text-slate-600 mb-2">Se enviará a los correos de la empresa: <span class="font-medium">{{ implode(', ', $correosEmpresa) }}</span></p>
            @endif
            <form method="post" action="{{ route('casino.soporte-factura.enviar', $cuentaGenerada->id_cuenta) }}" class="inline">
                @csrf
                <div class="inline-flex flex-wrap items-center gap-2">
                    @if(count($correosEmpresa) === 0)
                        <input type="email" name="email_contabilidad" value="{{ config('mail.contabilidad') }}"
                               placeholder="contabilidad@empresa.com"
                               class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-56 focus:border-slate-500 focus:ring-2 focus:ring-slate-500">
                    @endif
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-medium hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Enviar a contabilidad
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
