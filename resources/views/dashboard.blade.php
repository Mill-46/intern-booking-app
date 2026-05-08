@extends('layout')

@section('content')
@php
$statusCounts = collect($statusCounts ?? [])
    ->mapWithKeys(fn ($value, $key) => [$key => is_numeric($value) ? (int) $value : 0])
    ->all();
$fuelByMonth = collect($fuelByMonth ?? [])
    ->mapWithKeys(fn ($value, $key) => [$key => is_numeric($value) ? (float) $value : 0.0])
    ->all();
$vehicleUsage = collect($vehicleUsage ?? [])
    ->mapWithKeys(fn ($value, $key) => [$key => is_numeric($value) ? (int) $value : 0])
    ->all();
$siteUsage = collect($siteUsage ?? [])
    ->mapWithKeys(fn ($value, $key) => [$key => is_numeric($value) ? (int) $value : 0])
    ->all();
$pendingApprovalsByLevel = $pendingApprovalsByLevel ?? [1 => 0, 2 => 0];
$vehicleOwnership = $vehicleOwnership ?? ['company' => 0, 'rental' => 0];
$upcomingServices = $upcomingServices ?? collect();
$upcomingServices = collect($upcomingServices)
    ->filter(fn ($service) => is_object($service) && property_exists($service, 'service_type'))
    ->values();

$statusTotal = max(1, collect($statusCounts)->sum(fn ($value) => (int) $value));
$maxVehicleUsage = max(1, collect($vehicleUsage)->max() ?? 0);
$maxSiteUsage = max(1, collect($siteUsage)->max() ?? 0);
$maxFuelByMonth = max(1.0, collect($fuelByMonth)->max() ?? 0.0);

$statusSegments = [];
$statusOffset = 0;
foreach (\App\Models\Booking::statusLabels() as $statusKey => $statusLabel) {
    $count = (int) ($statusCounts[$statusKey] ?? 0);
    $width = (int) round(($count / $statusTotal) * 100);
    $statusSegments[] = [
        'label' => $statusLabel,
        'count' => $count,
        'offset' => $statusOffset,
        'width' => $width,
        'color' => match ($statusKey) {
            \App\Models\Booking::STATUS_REJECTED => '#dc2626',
            \App\Models\Booking::STATUS_COMPLETED => '#059669',
            \App\Models\Booking::STATUS_APPROVED_L2 => '#0284c7',
            \App\Models\Booking::STATUS_APPROVED_L1 => '#0ea5e9',
            \App\Models\Booking::STATUS_SUBMITTED => '#f59e0b',
            default => '#475569',
        },
    ];
    $statusOffset += $width;
}

$fuelValues = array_values($fuelByMonth);
$fuelLabels = array_keys($fuelByMonth);
$fuelPoints = collect($fuelValues)->map(function ($value, $index) use ($fuelValues, $maxFuelByMonth) {
    $count = max(1, count($fuelValues) - 1);
    $x = (int) round(($index / $count) * 100);
    $y = (int) round(100 - (((float) $value / $maxFuelByMonth) * 100));
    return "{$x},{$y}";
})->implode(' ');
@endphp

<x-page-header title="Dashboard" subtitle="Ringkasan performa pemesanan, approval, operasional, BBM, dan servis kendaraan." />

<div class="mb-6 grid gap-3 md:grid-cols-5">
    <div class="card">
        <p class="text-sm text-gray-600">Total Pemesanan</p>
        <p class="text-2xl font-bold text-gray-900">{{ $totalBookings }}</p>
    </div>
    <div class="card">
        <p class="text-sm text-gray-600">Aktif (Submitted/L1)</p>
        <p class="text-2xl font-bold text-gray-900">{{ $activeBookings }}</p>
    </div>
    <div class="card">
        <p class="text-sm text-gray-600">Lolos Approval Final</p>
        <p class="text-2xl font-bold text-gray-900">{{ $approvedBookings }}</p>
    </div>
    <div class="card">
        <p class="text-sm text-gray-600">Total BBM</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalFuelUsed, 2) }} L</p>
    </div>
    <div class="card">
        <p class="text-sm text-gray-600">Total Jarak Tempuh</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDistanceKm) }} km</p>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    <div class="card">
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Distribusi Status Booking</h2>
        <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">Stacked Status Chart</p>
            <svg viewBox="0 0 100 10" class="h-5 w-full overflow-hidden rounded-md">
                @foreach ($statusSegments as $segment)
                    @if ($segment['width'] > 0)
                        <rect
                            x="{{ $segment['offset'] }}"
                            y="0"
                            width="{{ $segment['width'] }}"
                            height="10"
                            fill="{{ $segment['color'] }}"
                        />
                    @endif
                @endforeach
            </svg>
        </div>
        <div class="space-y-3">
            @foreach (\App\Models\Booking::statusLabels() as $status => $label)
                @php
                    $count = (int) ($statusCounts[$status] ?? 0);
                    $percentage = (int) round(($count / $statusTotal) * 100);
                @endphp
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-800">{{ $label }}</span>
                        <span class="text-gray-600">{{ $count }} ({{ $percentage }}%)</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-slate-700" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Antrian Persetujuan</h2>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm text-amber-800">Pending Approval L1</p>
                <p class="mt-1 text-3xl font-bold text-amber-900">{{ (int) ($pendingApprovalsByLevel[1] ?? 0) }}</p>
            </div>
            <div class="rounded-lg border border-cyan-200 bg-cyan-50 p-4">
                <p class="text-sm text-cyan-800">Pending Approval L2</p>
                <p class="mt-1 text-3xl font-bold text-cyan-900">{{ (int) ($pendingApprovalsByLevel[2] ?? 0) }}</p>
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Kendaraan Milik Perusahaan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ (int) ($vehicleOwnership['company'] ?? 0) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Kendaraan Sewa</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ (int) ($vehicleOwnership['rental'] ?? 0) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-2">
    <div class="card">
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Top Kendaraan Terpakai</h2>
        <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">Mini Bar Chart</p>
            <svg viewBox="0 0 100 36" class="h-24 w-full">
                @foreach (array_values($vehicleUsage) as $index => $count)
                    @php
                        $totalBars = max(1, count($vehicleUsage));
                        $slot = 100 / $totalBars;
                        $barWidth = max(2, $slot - 2);
                        $x = ($index * $slot) + 1;
                        $height = ((int) $count / $maxVehicleUsage) * 30;
                        $y = 34 - $height;
                    @endphp
                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $height }}" fill="#0f172a" rx="1" />
                @endforeach
            </svg>
        </div>
        <div class="space-y-2">
            @forelse ($vehicleUsage as $registration => $count)
                @php $barWidth = (int) round(((int) $count / $maxVehicleUsage) * 100); @endphp
                <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 text-sm">
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <span class="truncate font-medium text-gray-800">{{ $registration }}</span>
                            <span class="shrink-0 text-gray-600">{{ (int) $count }} booking</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-slate-700" style="width: {{ $barWidth }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state title="Belum ada data" message="Data pemakaian kendaraan belum tersedia." />
            @endforelse
        </div>
    </div>

    <div class="card">
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Top Site Tujuan</h2>
        <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">Mini Bar Chart</p>
            <svg viewBox="0 0 100 36" class="h-24 w-full">
                @foreach (array_values($siteUsage) as $index => $count)
                    @php
                        $totalBars = max(1, count($siteUsage));
                        $slot = 100 / $totalBars;
                        $barWidth = max(2, $slot - 2);
                        $x = ($index * $slot) + 1;
                        $height = ((int) $count / $maxSiteUsage) * 30;
                        $y = 34 - $height;
                    @endphp
                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $height }}" fill="#0f766e" rx="1" />
                @endforeach
            </svg>
        </div>
        <div class="space-y-2">
            @forelse ($siteUsage as $siteName => $count)
                @php $barWidth = (int) round(((int) $count / $maxSiteUsage) * 100); @endphp
                <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 text-sm">
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <span class="truncate font-medium text-gray-800">{{ $siteName }}</span>
                            <span class="shrink-0 text-gray-600">{{ (int) $count }} perjalanan</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-teal-700" style="width: {{ $barWidth }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state title="Belum ada data" message="Data site tujuan belum tersedia." />
            @endforelse
        </div>
    </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-2">
    <div class="card">
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Tren Konsumsi BBM (6 Bulan)</h2>
        <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">Line Chart</p>
            <svg viewBox="0 0 100 100" class="h-28 w-full">
                <polyline fill="none" stroke="#0f766e" stroke-width="2" points="{{ $fuelPoints }}" />
                @foreach ($fuelValues as $index => $value)
                    @php
                        $count = max(1, count($fuelValues) - 1);
                        $x = (int) round(($index / $count) * 100);
                        $y = (int) round(100 - (((float) $value / $maxFuelByMonth) * 100));
                    @endphp
                    <circle cx="{{ $x }}" cy="{{ $y }}" r="1.7" fill="#0f766e" />
                @endforeach
            </svg>
            <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-600">
                @foreach ($fuelLabels as $label)
                    <span>{{ $label }}</span>
                @endforeach
            </div>
        </div>
        <div class="space-y-2">
            @foreach ($fuelByMonth as $period => $liter)
                @php $barWidth = (int) round((((float) $liter) / $maxFuelByMonth) * 100); @endphp
                <div class="rounded-md bg-gray-50 px-3 py-2 text-sm">
                    <div class="mb-1 flex items-center justify-between">
                        <span class="font-medium text-gray-800">{{ $period }}</span>
                        <span class="text-gray-600">{{ number_format((float) $liter, 2) }} L</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-200">
                        <div class="h-2 rounded-full bg-emerald-700" style="width: {{ $barWidth }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Jadwal Servis Terdekat</h2>
        <div class="space-y-2">
            @forelse ($upcomingServices as $service)
                <div class="rounded-md border border-gray-200 px-3 py-2">
                    <p class="text-sm font-semibold text-gray-900">{{ $service->vehicle?->registration_no ?? '-' }} - {{ $service->service_type }}</p>
                    <p class="text-xs text-gray-600">{{ optional($service->service_date)->format('Y-m-d') }} • {{ $service->workshop_name }}</p>
                </div>
            @empty
                <x-empty-state title="Belum ada jadwal servis" message="Tambahkan jadwal servis untuk monitoring perawatan kendaraan." />
            @endforelse
        </div>
    </div>
</div>
@endsection
