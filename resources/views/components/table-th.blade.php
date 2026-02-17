@props([])

<th {{ $attributes->merge(['class' => 'px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider']) }}>
    {{ $slot }}
</th>
