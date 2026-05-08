@extends('layout')

@section('content')
<x-page-header title="Buat Jadwal Servis" subtitle="Rencanakan service kendaraan secara terstruktur." />
<form method="POST" action="{{ route('vehicle-services.store') }}" class="form-card space-y-4">
    @csrf
    @php($vehicleService = new \App\Models\VehicleService())
    @include('vehicle-services.partials.form')
    <div class="flex justify-end"><button class="btn-primary">Simpan</button></div>
</form>
@endsection
