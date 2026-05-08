@props(['status'])

@php
$value = strtolower((string) $status);

$classes = match ($value) {
// DRAFT
'draft' => 'border-slate-300 bg-slate-100 text-slate-700',

// PENDING / ACTION REQUIRED
'submitted', 'pending', 'approved_l1', 'scheduled' => 'border-amber-300 bg-amber-100 text-amber-900',

// APPROVED / SUCCESS
'approved', 'approved_l2', 'confirmed', 'active', 'available', 'done', 'completed' => 'border-emerald-300 bg-emerald-100 text-emerald-900',

// REJECTED / DANGER
'rejected', 'inactive', 'maintenance' => 'border-red-300 bg-red-100 text-red-900',

// Fallback
default => 'border-slate-300 bg-slate-100 text-slate-700',
};

$labels = [
'draft' => 'Draf',
'submitted' => 'Menunggu L1',
'approved_l1' => 'Menunggu L2',
'approved_l2' => 'Siap Konfirmasi',
'confirmed' => 'Terkonfirmasi',
'completed' => 'Selesai',
'rejected' => 'Ditolak',
'pending' => 'Menunggu',
'approved' => 'Disetujui',
'active' => 'Aktif',
'inactive' => 'Tidak Aktif',
'maintenance' => 'Perawatan',
'available' => 'Tersedia',
'scheduled' => 'Terjadwal',
'done' => 'Selesai',
];

$label = $labels[$value] ?? str($value)->replace('_', ' ')->headline();
@endphp

<span {{ $attributes->class(['badge', $classes]) }}>{{ $label }}</span>
