@props(['type' => 'success'])

@php
$styles = [
    'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'info' => 'bg-sky-50 border-sky-200 text-sky-800',
];
$classes = 'rounded-lg border px-4 py-3 ' . ($styles[$type] ?? $styles['success']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
