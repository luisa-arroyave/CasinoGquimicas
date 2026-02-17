@props(['id' => null, 'name' => null, 'error' => null])

<select
    @if($id) id="{{ $id }}" @endif
    @if($name) name="{{ $name }}" @endif
    {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 transition-colors ' . ($error ? 'border-red-500' : '')]) }}
>
    {{ $slot }}
</select>
@if($error)
    <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
@endif
