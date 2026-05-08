@props(['title', 'subtitle' => null])

<div {{ $attributes->class(['section-header']) }}>
    <div>
        <h1>{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>

    @if(trim((string) $slot) !== '')
        <div class="flex flex-wrap items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
