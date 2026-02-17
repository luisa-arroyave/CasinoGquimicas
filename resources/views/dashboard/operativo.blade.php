@extends('layouts.app')

@section('title', 'Dashboard Operativo - ' . config('app.name'))
@section('page-title', 'Dashboard Operativo')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Panel Operativo</h2>
        <p class="mt-2 text-slate-600">
            Registra consumos de empleados y visitantes en punto de venta. Consulta consumos del día.
        </p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Registrar consumo (empleado)</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Escanear QR o documento</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Registrar consumo (visitante)</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Datos manuales</p>
        </div>
    </div>
</div>
@endsection
