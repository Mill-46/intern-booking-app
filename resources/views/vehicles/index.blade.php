@extends('layout')

@section('content')
<x-page-header title="Kendaraan" subtitle="Master data unit operasional perusahaan.">
    <a href="{{ route('vehicles.create') }}" class="btn-primary">Tambah Kendaraan</a>
</x-page-header>
<form method="GET" action="{{ route('vehicles.index') }}" class="card mb-4">
    <div class="grid gap-3 md:grid-cols-[1fr_auto]">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="Cari nopol, merk, model, atau jenis...">
        <button class="btn-primary w-full md:w-auto">Cari</button>
    </div>
</form>
<div class="table-wrap">
    <div class="table-scroll table-scroll-skeleton">
        <table class="table">
            <thead>
                <tr>
                    <th>Nopol</th>
                    <th>Jenis</th>
                    <th>Merek/Model</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->registration_no }}</td>
                    <td>{{ $vehicle->vehicle_type }}</td>
                    <td>{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                    <td><x-status-badge :status="$vehicle->status" /></td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn-xs">Edit</a>
                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-xs bg-rose-100 text-rose-700 hover:bg-rose-200" data-confirm="Hapus kendaraan ini?">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-slate-500">Belum ada data kendaraan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $vehicles->links() }}</div>
@endsection