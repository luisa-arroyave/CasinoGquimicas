@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800">Bienvenido, {{ auth()->user()->name }}</h2>
        <p class="mt-2 text-sm sm:text-base text-slate-600">
            Eres usuario con rol <strong>Empleado</strong>. Puedes solicitar consumos y consultar tu historial cuando esté disponible.
        </p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Mis consumos</p>
        <p class="mt-1 text-sm sm:text-base text-slate-700">Próximamente: historial de refrigerio, almuerzo y cena.</p>
    </div>
</div>
@endsection
