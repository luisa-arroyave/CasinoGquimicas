@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden']) }}>
    @if($title || $subtitle)
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            @if($title)
                <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="mt-0.5 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
