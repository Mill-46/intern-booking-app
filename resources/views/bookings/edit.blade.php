@extends('layout')

@section('content')
<x-page-header title="Edit Pemesanan #{{ $booking->id }}" subtitle="Perbarui detail sebelum proses approval." />
<form method="POST" action="{{ route('bookings.update', $booking) }}" class="form-card space-y-4">
    @csrf
    @method('PUT')

    @include('bookings.partials.form', [
        'booking' => $booking,
        'vehicles' => $vehicles,
        'drivers' => $drivers,
        'sites' => $sites,
        'approversL1' => $approversL1,
        'approversL2' => $approversL2,
    ])

    <div class="flex justify-end">
        <button class="btn-primary">Perbarui</button>
    </div>
</form>
@endsection
