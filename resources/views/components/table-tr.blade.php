@props([])

<tr {{ $attributes->merge(['class' => 'hover:bg-slate-50 transition-colors even:bg-slate-50/50']) }}>
    {{ $slot }}
</tr>
