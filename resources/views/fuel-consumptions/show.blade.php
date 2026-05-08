@extends('layout')

@section('content')
<x-page-header title="Detail Catatan BBM">
    <a href="{{ route('fuel-consumptions.index') }}" class="btn-soft">Kembali</a>
</x-page-header>

<div class="card">
    <div class="details-grid">
        <p><span class="text-slate-500">Nomor Booking:</span> BK-{{ str_pad((string) $fuelConsumption->booking_id, 5, '0', STR_PAD_LEFT) }}</p>
        <p><span class="text-slate-500">Kendaraan:</span> {{ $fuelConsumption->vehicle->registration_no }}</p>
        <p><span class="text-slate-500">Volume BBM:</span> {{ number_format((float) $fuelConsumption->fuel_used, 2, ',', '.') }} liter</p>
        <p><span class="text-slate-500">Waktu Pencatatan:</span> {{ $fuelConsumption->recorded_at?->format('d M Y H:i') }}</p>
        <p><span class="text-slate-500">Dibuat Pada:</span> {{ $fuelConsumption->created_at?->format('d M Y H:i') }}</p>
    </div>
</div>
@endsection
