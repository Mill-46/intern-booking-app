@extends('layout')

@section('content')
<x-page-header title="Edit Jadwal Servis" subtitle="Perbarui jadwal, status, dan detail servis." />
<form method="POST" action="{{ route('vehicle-services.update', $vehicleService) }}" class="form-card space-y-4">
    @csrf
    @method('PUT')
    @include('vehicle-services.partials.form')
    <div class="flex justify-end"><button class="btn-primary">Perbarui</button></div>
</form>
@endsection
