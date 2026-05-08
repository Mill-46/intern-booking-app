@extends('layout')

@section('content')
<x-page-header title="Jadwal Servis Kendaraan" subtitle="Monitoring servis terjadwal dan riwayat servis unit.">
    <a href="{{ route('vehicle-services.create') }}" class="btn-primary">Buat Jadwal</a>
</x-page-header>

<form method="GET" action="{{ route('vehicle-services.index') }}" class="card mb-4">
    <div class="grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="Cari kendaraan, jenis servis, atau bengkel...">
        <select name="status" class="field">
            <option value="">Semua status</option>
            <option value="scheduled" @selected(($filters['status'] ?? '' )==='scheduled' )>Terjadwal</option>
            <option value="done" @selected(($filters['status'] ?? '' )==='done' )>Selesai</option>
        </select>
        <button class="btn-primary w-full sm:w-auto">Cari</button>
    </div>
</form>

<div class="table-wrap">
    <div class="table-scroll table-scroll-skeleton">
        <table class="table">
            <thead>
                <tr>
                    <th>Kendaraan</th>
                    <th>Tanggal Servis</th>
                    <th>Jenis Servis</th>
                    <th class="hidden md:table-cell">Bengkel</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicleServices as $service)
                <tr>
                    <td>{{ $service->vehicle->registration_no }}</td>
                    <td>{{ $service->service_date->format('d M Y') }}</td>
                    <td>
                        <p class="max-w-60 truncate" title="{{ $service->service_type }}">{{ $service->service_type }}</p>
                    </td>
                    <td class="hidden md:table-cell">{{ $service->workshop_name }}</td>
                    <td><x-status-badge :status="$service->status" /></td>
                    <td>
                        <a href="{{ route('vehicle-services.show', $service) }}" class="btn-xs">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-slate-500">Belum ada jadwal servis.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $vehicleServices->links() }}</div>
@endsection
