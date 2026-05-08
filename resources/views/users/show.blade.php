@extends('layout')

@section('content')
<x-page-header title="Detail Pengguna" />
<div class="card">
    <div class="grid gap-3 text-sm md:grid-cols-2">
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> <x-status-badge :status="$user->role" /></p>
    <p><strong>Dibuat:</strong> {{ $user->created_at?->format('Y-m-d H:i') }}</p>
    </div>
</div>
@endsection
