@extends('layout')

@section('content')
<x-page-header title="Antrian Persetujuan" subtitle="Review pemesanan dan putuskan sesuai level approval Anda." />
<div class="mb-4 rounded-2xl bg-slate-50 p-4 text-sm border border-slate-200">
    <p class="font-semibold text-slate-900">Panduan Alur</p>
    <ul class="mt-2 list-disc pl-5 text-slate-600 space-y-1">
        <li>Approve level 1 → pemesanan lanjut ke level 2</li>
        <li>Approve level 2 → pemesanan siap dikonfirmasi admin</li>
        <li>Reject di level mana pun → pemesanan berstatus Ditolak</li>
    </ul>
</div>

<div class="space-y-3">
    @foreach($approvals as $approval)
    <div class="card">
        <p class="font-semibold">Pemesanan #{{ $approval->booking->id }} (L{{ $approval->level }})</p>
        <p class="text-sm">Kendaraan: {{ $approval->booking->vehicle->registration_no }} | Pemohon: {{ $approval->booking->user->name }}</p>
        <p class="text-sm">Rute: {{ $approval->booking->originSite?->name ?? '-' }} → {{ $approval->booking->destinationSite?->name ?? $approval->booking->destination }}</p>
        <p class="text-sm">Status Pemesanan: {{ $approval->booking->statusLabel() }}</p>
        <p class="text-sm">Status Approval: <x-status-badge :status="$approval->status" /></p>
        @if($approval->status === 'pending')
        <div class="mt-2 grid gap-2 md:grid-cols-2">
            <form method="POST" action="{{ route('approvals.approve', $approval) }}" class="flex gap-1">
                @csrf
                <input name="comment" placeholder="Catatan persetujuan (opsional)" class="field">
                <button class="btn-success" data-confirm="Setujui pemesanan ini?">Setujui</button>
            </form>
            <form method="POST" action="{{ route('approvals.reject', $approval) }}" class="flex gap-1">
                @csrf
                <input name="comment" placeholder="Alasan penolakan" class="field">
                <button class="btn-danger" data-confirm="Tolak pemesanan ini?">Tolak</button>
            </form>
        </div>
        @endif
    </div>
    @endforeach
</div>
@if($approvals->isEmpty())
<x-empty-state title="Tidak ada approval aktif" message="Semua pengajuan sudah diproses atau belum ada pengajuan baru untuk level Anda." />
@endif
<div class="mt-4">{{ $approvals->links() }}</div>
@endsection