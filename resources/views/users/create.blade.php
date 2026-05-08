@extends('layout')

@section('content')
<x-page-header title="Buat Pengguna" subtitle="Tambahkan akun admin atau approver." />
<div class="form-card">

    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
        @csrf
        @include('users.partials-form')
        <x-button type="primary">Simpan</x-button>
    </form>
</div>
@endsection
