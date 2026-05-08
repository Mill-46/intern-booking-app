@extends('layout')

@section('content')
<x-page-header title="Catatan BBM" subtitle="Pantau konsumsi BBM per kendaraan dan pemesanan.">
    <a href="{{ route('fuel-consumptions.create') }}" class="btn-primary">Tambah Catatan</a>
</x-page-header>

<form method="GET" action="{{ route('fuel-consumptions.index') }}" class="card mb-4">
    <div class="grid gap-3 md:grid-cols-[1.5fr_auto]">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="Cari booking, kendaraan, atau site...">
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
                    <th>Volume BBM</th>
                    <th class="hidden md:table-cell">Waktu Pencatatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fuelConsumptions as $fuelConsumption)
                <tr>
                    <td class="font-semibold">BK-{{ str_pad((string) $fuelConsumption->booking_id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $fuelConsumption->vehicle->registration_no }}</td>
                    <td>{{ number_format((float) $fuelConsumption->fuel_used, 2, ',', '.') }} liter</td>
                    <td class="hidden md:table-cell">{{ $fuelConsumption->recorded_at?->format('d M Y H:i') }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route('fuel-consumptions.show', $fuelConsumption) }}" class="btn-xs">Detail</a>
                            <a href="{{ route('fuel-consumptions.edit', $fuelConsumption) }}" class="btn-xs">Edit</a>
                            <form method="POST" action="{{ route('fuel-consumptions.destroy', $fuelConsumption) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-xs bg-rose-100 text-rose-700 hover:bg-rose-200" data-confirm="Hapus catatan BBM ini?">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-slate-500">Belum ada catatan BBM.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $fuelConsumptions->links() }}</div>
@endsection