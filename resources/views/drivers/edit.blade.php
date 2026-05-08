@extends('layout')

@section('content')
<x-page-header title="Edit Pengemudi" subtitle="Perbarui data pengemudi." />
<form method="POST" action="{{ route('drivers.update', $driver) }}" class="form-card space-y-4">
    @csrf
    @method('PUT')
    @include('drivers.partials.form')
    <div class="flex justify-end"><button class="btn-primary">Perbarui</button></div>
</form>
@endsection
