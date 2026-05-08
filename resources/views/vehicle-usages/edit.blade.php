@extends('layout')

@section('content')
<x-page-header title="Edit Riwayat Pemakaian #{{ $vehicleUsage->id }}" />
<div class="form-card">
    <form method="POST" action="{{ route('vehicle-usages.update', $vehicleUsage) }}" class="space-y-4">
        @csrf
        @method('PATCH')
        @include('vehicle-usages.partials-form')
    </form>
</div>
@endsection
