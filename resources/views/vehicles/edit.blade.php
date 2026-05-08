@extends('layout')

@section('content')
<x-page-header title="Edit Kendaraan" subtitle="Perbarui informasi kendaraan." />
<form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="form-card space-y-4">
    @csrf
    @method('PUT')
    @include('vehicles.partials.form')
    <div class="flex justify-end"><button class="btn-primary">Perbarui</button></div>
</form>
@endsection
