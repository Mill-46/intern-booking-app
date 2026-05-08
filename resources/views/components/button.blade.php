@props([
    'type' => 'primary', // primary, soft, success, danger, etc.
    'size' => 'md',   // xs, sm, md, lg
    'class' => ''
])

@php
    // Map variant to Tailwind utility classes
    $variantClasses = [
        'primary' => 'border border-emerald-900/90 bg-emerald-900 text-white hover:bg-emerald-800',
        'soft'    => 'border border-emerald-900/20 bg-white text-emerald-950 hover:bg-emerald-50',
        'success' => 'border border-emerald-700 bg-emerald-700 text-white hover:bg-emerald-600',
        'danger'  => 'border border-red-800 bg-red-700 text-white hover:bg-red-600',
    ];

    // Map size to padding / font size
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs rounded-lg',
        'sm' => 'px-3 py-1.5 text-sm rounded-xl',
        'md' => 'px-4 py-2 text-sm rounded-xl',
        'lg' => 'px-5 py-3 text-base rounded-xl',
    ];

    $variant = $variantClasses[$type] ?? $variantClasses['primary'];
    $size    = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<button {{ $attributes->merge(['class' => trim("inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:cursor-not-allowed disabled:opacity-50 {$variant} {$size} {$class}")]) }}>
    {{ $slot }}
</button>
