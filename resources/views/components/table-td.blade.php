@props([])

<td {{ $attributes->merge(['class' => 'px-6 py-4 text-sm text-slate-800 whitespace-nowrap') }}>
    {{ $slot }}
</td>
