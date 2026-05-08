@extends('layout')

@section('content')
<x-page-header title="Riwayat Pemakaian Kendaraan" subtitle="Data realisasi perjalanan kendaraan beserta odometer.">
    <a href="{{ route('vehicle-usages.create') }}" class="btn-primary">Tambah Riwayat</a>
</x-page-header>

<form method="GET" action="{{ route('vehicle-usages.index') }}" class="card mb-4">
    <div class="grid gap-3 md:grid-cols-[1.5fr_auto]">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="Cari booking, kendaraan, pengemudi, atau site...">
        <button class="btn-primary w-full md:w-auto">Cari</button>
    </div>
</form>

<div class="table-wrap">
    <div class="table-scroll table-scroll-skeleton">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Booking</th>
                    <th>Kendaraan</th>
                    <th class="hidden md:table-cell">Pengemudi</th>
                    <th>Rute</th>
                    <th>Odometer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicleUsages as $vehicleUsage)
                <tr>
                    <td class="font-semibold">BK-{{ str_pad((string) $vehicleUsage->booking_id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $vehicleUsage->vehicle->registration_no }}</td>
                    <td class="hidden md:table-cell">{{ $vehicleUsage->driver->name }}</td>
                    <td>
                        <p class="max-w-55 truncate" title="{{ $vehicleUsage->originSite?->name ?? '-' }} → {{ $vehicleUsage->destinationSite?->name ?? '-' }}">{{ $vehicleUsage->originSite?->name ?? '-' }} → {{ $vehicleUsage->destinationSite?->name ?? '-' }}</p>
                    </td>
                    <td>
                        <p>{{ number_format($vehicleUsage->odometer_start) }} - {{ number_format($vehicleUsage->odometer_end) }}</p>
                        <p class="text-xs text-slate-500">Jarak {{ number_format($vehicleUsage->distanceKm()) }} km</p>
                    </td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route('vehicle-usages.show', $vehicleUsage) }}" class="btn-xs">Detail</a>
                            <a href="{{ route('vehicle-usages.edit', $vehicleUsage) }}" class="btn-xs">Edit</a>
                            <form method="POST" action="{{ route('vehicle-usages.destroy', $vehicleUsage) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-xs bg-rose-100 text-rose-700 hover:bg-rose-200" data-confirm="Hapus riwayat pemakaian ini?">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-slate-500">Belum ada data pemakaian kendaraan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $vehicleUsages->links() }}</div>
@endsection
