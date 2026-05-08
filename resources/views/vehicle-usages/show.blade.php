@extends('layout')

@section('content')
<x-page-header title="Detail Riwayat Pemakaian">
    <a href="{{ route('vehicle-usages.index') }}" class="btn-soft">Kembali</a>
</x-page-header>

<div class="card">
    <div class="details-grid">
        <p><span class="text-slate-500">Nomor Booking:</span> BK-{{ str_pad((string) $vehicleUsage->booking_id, 5, '0', STR_PAD_LEFT) }}</p>
        <p><span class="text-slate-500">Kendaraan:</span> {{ $vehicleUsage->vehicle->registration_no }}</p>
        <p><span class="text-slate-500">Pengemudi:</span> {{ $vehicleUsage->driver->name }}</p>
        <p><span class="text-slate-500">Rute:</span> {{ $vehicleUsage->originSite?->name ?? '-' }} → {{ $vehicleUsage->destinationSite?->name ?? '-' }}</p>
        <p><span class="text-slate-500">Waktu Mulai:</span> {{ $vehicleUsage->started_at?->format('d M Y H:i') }}</p>
        <p><span class="text-slate-500">Waktu Selesai:</span> {{ $vehicleUsage->ended_at?->format('d M Y H:i') }}</p>
        <p><span class="text-slate-500">Odometer Awal:</span> {{ number_format($vehicleUsage->odometer_start) }} km</p>
        <p><span class="text-slate-500">Odometer Akhir:</span> {{ number_format($vehicleUsage->odometer_end) }} km</p>
        <p><span class="text-slate-500">Jarak Tempuh:</span> {{ number_format($vehicleUsage->distanceKm()) }} km</p>
    </div>

    <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm">
        <p><span class="text-slate-500">Catatan:</span> {{ $vehicleUsage->notes ?: '-' }}</p>
    </div>
</div>
@endsection
