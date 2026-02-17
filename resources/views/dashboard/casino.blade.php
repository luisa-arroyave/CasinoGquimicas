@extends('layouts.app')

@section('title', 'Dashboard Casino - ' . config('app.name'))
@section('page-title', 'Dashboard Casino')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Panel Casino / Restaurante</h2>
        <p class="mt-2 text-slate-600">
            Registra consumos, consulta horarios, genera cuentas de cobro y revisa ventas del día.
        </p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Registro de consumos</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Registrar vale</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Horarios (Refrigerio / Almuerzo / Cena)</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Consultar</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Cuentas de cobro</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Generar y descargar</p>
        </div>
    </div>
</div>
@endsection
