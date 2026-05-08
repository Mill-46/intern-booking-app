@extends('layout')

@section('content')
<x-page-header title="Detail Pengemudi {{ $driver->name }}" />
<div class="card">
    <div class="grid gap-3 text-sm md:grid-cols-2">
        <p><span class="text-slate-500">Telepon:</span> {{ $driver->phone }}</p>
        <p><span class="text-slate-500">Nomor SIM:</span> {{ $driver->license_no }}</p>
        <p><span class="text-slate-500">Berlaku Sampai:</span> {{ $driver->license_expiry?->format('Y-m-d') }}</p>
        <p><span class="text-slate-500">Status:</span> <x-status-badge :status="$driver->status" /></p>
    </div>
</div>
@endsection
