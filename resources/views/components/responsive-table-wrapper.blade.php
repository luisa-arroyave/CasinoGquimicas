@props(['cardView' => false])
{{-- Envuelve una tabla: en móvil permite scroll horizontal o muestra como tarjetas si la tabla tiene clase table-cards-mobile --}}
<div class="w-full min-w-0 -mx-4 sm:mx-0">
    <div class="overflow-x-auto overflow-y-visible rounded-none sm:rounded-xl border-0 sm:border border-slate-200 bg-white shadow-sm">
        {{ $slot }}
    </div>
</div>
