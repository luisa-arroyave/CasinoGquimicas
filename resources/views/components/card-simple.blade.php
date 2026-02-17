@props([])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm p-6']) }}>
    {{ $slot }}
</div>
