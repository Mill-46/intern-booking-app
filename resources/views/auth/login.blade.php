@extends('layout')

@section('content')
<div class="flex min-h-[72vh] w-full items-center justify-center px-3 sm:px-6">
    <div class="card w-full max-w-4xl p-6 sm:p-8 lg:p-10">
        <h1 class="mb-1 text-2xl font-semibold sm:text-3xl">Masuk</h1>
        <p class="mb-7 text-sm text-slate-500 sm:text-base">Masuk untuk mengelola pemesanan kendaraan.</p>
        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-1 block text-sm">Surel</label>
                <input type="email" name="email" value="{{ old('email') }}" class="field">
            </div>
            <div>
                <label class="mb-1 block text-sm">Kata Sandi</label>
                <input type="password" name="password" class="field">
            </div>
            <button class="btn-primary w-full">Masuk</button>
        </form>
    </div>
</div>
@endsection
