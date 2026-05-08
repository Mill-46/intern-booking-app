@extends('layout')

@section('content')
<x-page-header title="Tambah Pengemudi" subtitle="Tambahkan data driver operasional." />
<form method="POST" action="{{ route('drivers.store') }}" class="form-card space-y-4">
    @csrf
    @include('drivers.partials.form')
    <div class="flex justify-end"><button class="btn-primary">Simpan</button></div>
</form>
@endsection
