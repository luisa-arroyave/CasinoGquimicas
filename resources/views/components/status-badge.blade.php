@props(['estado' => ''])

@php
$estado = strtoupper(trim($estado));
$styles = [
    'ENTREGADO' => 'bg-emerald-100 text-emerald-800',
    'SOLICITADO' => 'bg-amber-100 text-amber-800',
    'PENDIENTE' => 'bg-amber-100 text-amber-800',
    'ANULADO' => 'bg-red-100 text-red-800',
];
$class = $styles[$estado] ?? 'bg-slate-100 text-slate-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ' . $class]) }}>
    {{ $estado ?: $slot }}
</span>
