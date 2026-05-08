@props([
    'title' => 'Belum ada data',
    'message' => 'Data akan muncul di sini setelah Anda menambahkan data baru.',
])

<div {{ $attributes->class(['card text-center']) }}>
    <p class="text-base font-semibold text-slate-900">{{ $title }}</p>
    <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
</div>
