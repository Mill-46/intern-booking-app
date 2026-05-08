@extends('layout')

@section('content')
<div class="card">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Detail Pemesanan #{{ $booking->id }}</h1>
        <x-status-badge :status="$booking->status" />
    </div>

    <div class="grid gap-3 text-sm md:grid-cols-2">
        <p><span class="text-slate-500">Pemohon:</span> {{ $booking->user->name }}</p>
        <p><span class="text-slate-500">Kendaraan:</span> {{ $booking->vehicle->registration_no }}</p>
        <p><span class="text-slate-500">Pengemudi:</span> {{ $booking->driver->name }}</p>
        <p><span class="text-slate-500">Waktu:</span> {{ $booking->start_at }} - {{ $booking->end_at }}</p>
        <p><span class="text-slate-500">Site Asal:</span> {{ $booking->originSite?->name ?? '-' }}</p>
        <p><span class="text-slate-500">Site Tujuan:</span> {{ $booking->destinationSite?->name ?? '-' }}</p>
        <p><span class="text-slate-500">Approver L1:</span> {{ $booking->approverL1?->name ?? '-' }}</p>
        <p><span class="text-slate-500">Approver L2:</span> {{ $booking->approverL2?->name ?? '-' }}</p>
    </div>

    <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm">
        <p><span class="text-slate-500">Detail Tujuan:</span> {{ $booking->destination }}</p>
        <p class="mt-2"><span class="text-slate-500">Tujuan Pemakaian:</span> {{ $booking->purpose }}</p>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @if(auth()->user()->isAdmin() && $booking->status === \App\Models\Booking::STATUS_APPROVED_L2)
        <form method="POST" action="{{ route('bookings.confirm', $booking) }}" class="inline">
            @csrf
            <button class="btn-success" data-confirm="Konfirmasi pemesanan ini?">Konfirmasi Pemesanan</button>
        </form>
        @endif

        @if(auth()->user()->isAdmin() && $booking->status === \App\Models\Booking::STATUS_CONFIRMED)
        <form method="POST" action="{{ route('bookings.complete', $booking) }}" class="inline">
            @csrf
            <button class="btn-primary" data-confirm="Tandai pemesanan ini sebagai selesai?">Tandai Selesai</button>
        </form>
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="section-header">
        <div>
            <h2 class="mb-1 font-semibold">Riwayat Persetujuan</h2>
            <p class="text-sm text-slate-500">Audit trail keputusan per level approval.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Booking Status</span>
            <x-status-badge :status="$booking->status" />
        </div>
    </div>

    @foreach($booking->approvals as $approval)
    <div class="mb-2 rounded-lg border border-slate-200 p-3 text-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="font-medium">
                Level {{ $approval->level }} — {{ $approval->approver->name }}
            </p>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diputus</span>
                <span class="text-xs text-slate-600">
                    {{ $approval->acted_at?->format('d M Y H:i') ?? '—' }}
                </span>
            </div>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Keputusan</span>
            <x-status-badge :status="$approval->status" />
            @if($approval->comment)
            <span class="text-xs text-slate-600">— {{ $approval->comment }}</span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection