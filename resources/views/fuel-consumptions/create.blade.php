@extends('layout')

@section('content')
<x-page-header title="Tambah Catatan BBM" subtitle="Catat konsumsi BBM berdasarkan pemesanan kendaraan." />

<div class="form-card">
    <form method="POST" action="{{ route('fuel-consumptions.store') }}" class="space-y-4">
        @csrf
        @include('fuel-consumptions.partials-form')

        <button class="btn-primary">Simpan</button>
    </form>
</div>
@endsection
