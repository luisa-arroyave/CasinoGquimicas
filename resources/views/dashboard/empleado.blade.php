@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800">Bienvenido, {{ auth()->user()->name }}</h2>
        <p class="mt-2 text-sm sm:text-base text-slate-600">
        Puedes solicitar consumos y consultar tu historial.
        </p>
    </div>

    {{-- Quincena 1 al 15 --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm overflow-hidden">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Mis consumos del 1 al 15 — {{ $mesActual ?? now()->translatedFormat('F Y') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">No.</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">Tipo de comida</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-slate-600 uppercase">Precio empleado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse(($consumosQuincena1_15 ?? collect()) as $c)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-sm text-slate-800">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 text-sm text-slate-800">{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm text-slate-800">{{ $c->display_consumidor }}</td>
                        <td class="px-4 py-2 text-sm text-slate-600">{{ $c->display_horario }}</td>
                        <td class="px-4 py-2 text-sm text-right text-slate-800">{{ $c->display_precio_empleado }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500 text-sm">No hay consumos en este período.</td></tr>
                    @endforelse
                </tbody>
                @if(isset($consumosQuincena1_15) && $consumosQuincena1_15->isNotEmpty())
                <tfoot class="bg-slate-100 border-t-2 border-slate-300">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-sm font-semibold text-slate-800 text-right">Total:</td>
                        <td class="px-4 py-2 text-sm font-semibold text-slate-800 text-right">$ {{ number_format($consumosQuincena1_15->sum('precio_empleado'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Quincena 16 al 31 --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm overflow-hidden">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Mis consumos del 16 al 31 — {{ $mesActual ?? now()->translatedFormat('F Y') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">No.</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-600 uppercase">Tipo de comida</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-slate-600 uppercase">Precio empleado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse(($consumosQuincena16_31 ?? collect()) as $c)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-sm text-slate-800">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 text-sm text-slate-800">{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm text-slate-800">{{ $c->display_consumidor }}</td>
                        <td class="px-4 py-2 text-sm text-slate-600">{{ $c->display_horario }}</td>
                        <td class="px-4 py-2 text-sm text-right text-slate-800">{{ $c->display_precio_empleado }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500 text-sm">No hay consumos en este período.</td></tr>
                    @endforelse
                </tbody>
                @if(isset($consumosQuincena16_31) && $consumosQuincena16_31->isNotEmpty())
                <tfoot class="bg-slate-100 border-t-2 border-slate-300">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-sm font-semibold text-slate-800 text-right">Total:</td>
                        <td class="px-4 py-2 text-sm font-semibold text-slate-800 text-right">$ {{ number_format($consumosQuincena16_31->sum('precio_empleado'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
