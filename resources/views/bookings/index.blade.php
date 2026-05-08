@extends('layout')

@section('content')
<x-page-header title="Pemesanan Kendaraan" subtitle="Kelola permintaan kendaraan, approval, dan pelaporan periodik.">
    <x-button href="{{ route('bookings.create') }}" type="primary">Buat Pemesanan</x-button>
</x-page-header>

<form method="GET" action="{{ route('bookings.index') }}" class="card mb-4 space-y-4">
    <div class="grid gap-3 md:grid-cols-[1fr_auto]">
        <input
            type="text"
            name="q"
            value="{{ $filters['q'] ?? '' }}"
            class="field"
            placeholder="Cari pemesan, kendaraan (plat), pengemudi, tujuan, atau keperluan...">
        <div class="grid gap-2 sm:justify-end">
            <x-button type="primary" class="w-full md:w-auto">Cari</x-button>
            <x-button type="soft" class="w-full md:w-auto">Reset</x-button>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900">Filter lanjutan</p>
                <p class="text-xs text-slate-500">Gunakan filter ini untuk mempersempit hasil pencarian pemesanan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('bookings.index', ['preset' => 'today']) }}" class="btn-soft">Hari Ini</a>
                <a href="{{ route('bookings.index', ['preset' => 'this_month']) }}" class="btn-soft">Bulan Ini</a>
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Mulai</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="field">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Selesai</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="field">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select name="status" class="field">
                    <option value="">Semua status</option>
                    @foreach([
                    \App\Models\Booking::STATUS_DRAFT,
                    \App\Models\Booking::STATUS_SUBMITTED,
                    \App\Models\Booking::STATUS_APPROVED_L1,
                    \App\Models\Booking::STATUS_APPROVED_L2,
                    \App\Models\Booking::STATUS_CONFIRMED,
                    \App\Models\Booking::STATUS_COMPLETED,
                    \App\Models\Booking::STATUS_REJECTED,
                    ] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '' )===$status)>{{ \App\Models\Booking::statusLabels()[$status] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Site Asal</label>
                <select name="origin_site_id" class="field">
                    <option value="">Semua site asal</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" @selected((string) ($filters['origin_site_id'] ?? '' )===(string) $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Site Tujuan</label>
                <select name="destination_site_id" class="field">
                    <option value="">Semua site tujuan</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" @selected((string) ($filters['destination_site_id'] ?? '' )===(string) $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kendaraan</label>
                <select name="vehicle_id" class="field">
                    <option value="">Semua kendaraan</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" @selected((string) ($filters['vehicle_id'] ?? '' )===(string) $vehicle->id)>{{ $vehicle->registration_no }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pengemudi</label>
                <select name="driver_id" class="field">
                    <option value="">Semua pengemudi</option>
                    @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" @selected((string) ($filters['driver_id'] ?? '' )===(string) $driver->id)>{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pemesan</label>
                <select name="requester_id" class="field">
                    <option value="">Semua pemesan</option>
                    @foreach($requesters as $requester)
                    <option value="{{ $requester->id }}" @selected((string) ($filters['requester_id'] ?? '' )===(string) $requester->id)>{{ $requester->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>

<form method="GET" action="{{ route('exports.bookings') }}" class="card mb-4">
    <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
    <input type="hidden" name="from" value="{{ $filters['from'] ?? '' }}">
    <input type="hidden" name="to" value="{{ $filters['to'] ?? '' }}">
    <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
    <input type="hidden" name="vehicle_id" value="{{ $filters['vehicle_id'] ?? '' }}">
    <input type="hidden" name="driver_id" value="{{ $filters['driver_id'] ?? '' }}">
    <input type="hidden" name="requester_id" value="{{ $filters['requester_id'] ?? '' }}">
    <input type="hidden" name="origin_site_id" value="{{ $filters['origin_site_id'] ?? '' }}">
    <input type="hidden" name="destination_site_id" value="{{ $filters['destination_site_id'] ?? '' }}">
    <button class="btn-success">Export Excel Sesuai Filter</button>
</form>

<div class="table-wrap">
    <div class="table-scroll table-scroll-skeleton">
        <table class="table">
            <thead>
                <tr>
                    <th class="w-30">No. Booking</th>
                    <th class="w-45">Vehicle</th>
                    <th class="hidden md:table-cell w-35">Driver</th>
                    <th class="min-w-45">Route</th>
                    <th class="hidden lg:table-cell w-45">Time</th>
                    <th class="w-30">Status</th>
                    <th class="w-40">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td class="font-semibold">BK-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="min-w-0">
                        <p class="font-medium truncate" title="{{ $booking->vehicle->registration_no }}">{{ $booking->vehicle->registration_no }}</p>
                        <p class="text-xs text-slate-500 truncate" title="{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                    </td>
                    <td class="hidden md:table-cell min-w-0">{{ $booking->driver->name }}</td>
                    <td class="min-w-0">
                        <p class="truncate" title="{{ $booking->originSite?->name ?? '-' }} → {{ $booking->destinationSite?->name ?? $booking->destination }}">{{ $booking->originSite?->name ?? '-' }} → {{ $booking->destinationSite?->name ?? $booking->destination }}</p>
                        <p class="text-xs text-slate-500 truncate" title="{{ $booking->purpose }}">{{ $booking->purpose }}</p>
                    </td>
                    <td class="hidden lg:table-cell min-w-0">
                        <p>{{ $booking->start_at?->format('d M Y H:i') }}</p>
                        <p class="text-xs text-slate-500">s/d {{ $booking->end_at?->format('d M Y H:i') }}</p>
                    </td>
                    <td><x-status-badge :status="$booking->status" /></td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn-xs">Detail</a>
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn-xs">Ubah</a>
                            @if(auth()->user()->isAdmin() && $booking->status === \App\Models\Booking::STATUS_DRAFT)
                            <form method="POST" action="{{ route('bookings.submit', $booking) }}" class="inline">
                                @csrf
                                <button class="btn-xs btn-success" data-confirm="Ajukan pemesanan ini ke proses persetujuan?">Ajukan</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-slate-500">Belum ada data pemesanan kendaraan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Card View --}}
<div class="md:hidden space-y-4">
    @forelse($bookings as $booking)
    <div class="card">
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-semibold text-sm">BK-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <x-status-badge :status="$booking->status" />
                </div>
                <div class="space-y-1 text-sm">
                    <p><strong>Kendaraan:</strong> {{ $booking->vehicle->registration_no }} ({{ $booking->vehicle->brand }} {{ $booking->vehicle->model }})</p>
                    <p><strong>Pengemudi:</strong> {{ $booking->driver->name }}</p>
                    <p><strong>Rute:</strong> {{ $booking->originSite?->name ?? '-' }} → {{ $booking->destinationSite?->name ?? $booking->destination }}</p>
                    <p><strong>Keperluan:</strong> {{ $booking->purpose }}</p>
                    <p><strong>Waktu:</strong> {{ $booking->start_at?->format('d M Y H:i') }} - {{ $booking->end_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="{{ route('bookings.show', $booking) }}" class="btn-xs">Detail</a>
            <a href="{{ route('bookings.edit', $booking) }}" class="btn-xs">Ubah</a>
            @if(auth()->user()->isAdmin() && $booking->status === \App\Models\Booking::STATUS_DRAFT)
            <form method="POST" action="{{ route('bookings.submit', $booking) }}" class="inline">
                @csrf
                <button class="btn-xs btn-success" data-confirm="Ajukan pemesanan ini ke proses persetujuan?">Ajukan</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="card text-center text-slate-500">Belum ada data pemesanan kendaraan.</div>
    @endforelse
</div>

<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
