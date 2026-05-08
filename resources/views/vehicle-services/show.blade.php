@extends('layout')

@section('content')
<x-page-header title="Detail Servis Kendaraan">
    <a href="{{ route('vehicle-services.edit', $vehicleService) }}" class="btn-soft">Edit</a>
    <form method="POST" action="{{ route('vehicle-services.destroy', $vehicleService) }}">
        @csrf
        @method('DELETE')
        <button class="btn-danger" data-confirm="Hapus data servis ini?">Hapus</button>
    </form>
</x-page-header>

<div class="card">
    <div class="details-grid">
        <p><span class="text-slate-500">Kendaraan:</span> {{ $vehicleService->vehicle->registration_no }}</p>
        <p><span class="text-slate-500">Tanggal Servis:</span> {{ $vehicleService->service_date->format('d M Y') }}</p>
        <p><span class="text-slate-500">Jenis Servis:</span> {{ $vehicleService->service_type }}</p>
        <p><span class="text-slate-500">Bengkel:</span> {{ $vehicleService->workshop_name }}</p>
        <p><span class="text-slate-500">Biaya:</span> Rp {{ number_format((float) $vehicleService->cost, 0, ',', '.') }}</p>
        <p><span class="text-slate-500">Status:</span> <x-status-badge :status="$vehicleService->status" /></p>
    </div>

    <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm">
        <p><span class="text-slate-500">Catatan:</span> {{ $vehicleService->notes ?: '-' }}</p>
    </div>
</div>
@endsection
