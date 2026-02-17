@extends('layouts.app')

@section('title', 'Dashboard Gestión Humana - ' . config('app.name'))
@section('page-title', 'Dashboard Gestión Humana')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Gestión Humana</h2>
        <p class="mt-2 text-slate-600">
            Gestiona empleados, tipos de usuario, asignación a empresas y consulta de consumos por persona.
        </p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Empleados / Usuarios</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Consulta y gestión</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Consumos por empleado</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">Historial y reportes</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Tipos de usuario</p>
            <p class="mt-1 text-xl font-semibold text-slate-800">FIJO, TEMPORAL, SENA, etc.</p>
        </div>
    </div>
</div>
@endsection
