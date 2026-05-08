@extends('layout')

@section('content')
<x-page-header title="Edit Pengguna" subtitle="Perbarui role dan data akun." />
<div class="form-card">

    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('users.partials-form', ['user' => $user])
        <button class="btn-primary">Perbarui</button>
    </form>
</div>
@endsection
