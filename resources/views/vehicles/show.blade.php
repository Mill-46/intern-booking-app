@extends('layout')

@section('content')
<x-page-header title="Detail Kendaraan {{ $vehicle->registration_no }}" />
<div class="card">
    <div class="grid gap-3 text-sm md:grid-cols-2">
        <p><span class="text-slate-500">Merek/Model:</span> {{ $vehicle->brand }} {{ $vehicle->model }}</p>
        <p><span class="text-slate-500">Jenis:</span> {{ $vehicle->vehicle_type }}</p>
        <p><span class="text-slate-500">Kapasitas BBM:</span> {{ $vehicle->fuel_capacity }} L</p>
        <p><span class="text-slate-500">Kilometer:</span> {{ number_format((float) $vehicle->mileage) }} km</p>
        <p><span class="text-slate-500">Kepemilikan:</span> {{ $vehicle->owner }}</p>
        <p><span class="text-slate-500">Status:</span> <x-status-badge :status="$vehicle->status" /></p>
    </div>
</div>
@endsection
