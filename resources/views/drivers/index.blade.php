@extends('layout')

@section('content')
<x-page-header title="Pengemudi" subtitle="Daftar driver aktif dan status lisensi.">
    <a href="{{ route('drivers.create') }}" class="btn-primary">Tambah Pengemudi</a>
</x-page-header>
<form method="GET" action="{{ route('drivers.index') }}" class="card mb-4">
    <div class="grid gap-3 md:grid-cols-[1fr_auto]">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="Cari nama, telepon, atau nomor SIM...">
        <button class="btn-primary w-full md:w-auto">Cari</button>
    </div>
</form>
<div class="table-wrap">
    <div class="table-scroll table-scroll-skeleton">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Nomor SIM</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $driver)
                <tr>
                    <td>{{ $driver->name }}</td>
                    <td>{{ $driver->phone }}</td>
                    <td>{{ $driver->license_no }}</td>
                    <td><x-status-badge :status="$driver->status" /></td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route('drivers.edit', $driver) }}" class="btn-xs">Edit</a>
                            <form method="POST" action="{{ route('drivers.destroy', $driver) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-xs bg-rose-100 text-rose-700 hover:bg-rose-200" data-confirm="Hapus pengemudi ini?">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-slate-500">Belum ada data pengemudi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $drivers->links() }}</div>
@endsection