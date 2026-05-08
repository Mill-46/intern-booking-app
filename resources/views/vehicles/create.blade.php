@extends('layout')

@section('content')
<x-page-header title="Tambah Kendaraan" subtitle="Masukkan data unit kendaraan baru." />
<form method="POST" action="{{ route('vehicles.store') }}" class="form-card space-y-4">
    @csrf
    @include('vehicles.partials.form')
    <div class="flex justify-end"><button class="btn-primary">Simpan</button></div>
</form>
@endsection
