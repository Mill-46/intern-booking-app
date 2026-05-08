@extends('layout')

@section('content')
<x-page-header title="Tambah Riwayat Pemakaian" subtitle="Input realisasi pemakaian dan odometer." />
<div class="form-card">
    <form method="POST" action="{{ route('vehicle-usages.store') }}" class="space-y-4">
        @csrf
        @include('vehicle-usages.partials-form')
    </form>
</div>
@endsection
