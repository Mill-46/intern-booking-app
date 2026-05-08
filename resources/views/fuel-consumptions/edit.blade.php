@extends('layout')

@section('content')
<x-page-header title="Edit Catatan BBM" subtitle="Sesuaikan data konsumsi BBM jika ada perubahan." />

<div class="form-card">
    <form method="POST" action="{{ route('fuel-consumptions.update', $fuelConsumption) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('fuel-consumptions.partials-form', ['fuelConsumption' => $fuelConsumption])

        <button class="btn-primary">Perbarui</button>
    </form>
</div>
@endsection
