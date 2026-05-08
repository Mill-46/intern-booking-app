@extends('layout')

@section('content')
<x-page-header title="Buat Pemesanan" subtitle="Lengkapi kendaraan, driver, rute, dan approver berjenjang." />
<form method="POST" action="{{ route('bookings.store') }}" class="form-card space-y-4">
    @csrf

    @include('bookings.partials.form', [
        'booking' => null,
        'vehicles' => $vehicles,
        'drivers' => $drivers,
        'sites' => $sites,
        'approversL1' => $approversL1,
        'approversL2' => $approversL2,
    ])

    <div class="flex justify-end">
        <button class="btn-primary">Simpan Draft</button>
    </div>
</form>
@endsection
