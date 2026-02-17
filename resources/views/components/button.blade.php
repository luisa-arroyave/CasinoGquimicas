@props(['variant' => 'primary', 'type' => 'button', 'icon' => null, 'href' => null])

@php
$base = 'inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:pointer-events-none';
$variants = [
    'primary' => 'bg-slate-800 text-white hover:bg-slate-700 focus:ring-slate-500',
    'secondary' => 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-slate-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
];
$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if(isset($href))
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<span class="shrink-0">{!! $icon !!}</span>@endif
    {{ $slot }}
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<span class="shrink-0">{!! $icon !!}</span>@endif
    {{ $slot }}
</button>
@endif
