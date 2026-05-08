@extends('layout')

@section('content')
<div class="section-header">
    <h1>Pengguna</h1>
    <a href="{{ route('users.create') }}" class="btn-primary">Buat Pengguna</a>
</div>

<div class="table-wrap">
    <div class="table-scroll table-scroll-skeleton">
    <table class="table">
        <thead>
        <tr>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-left">Role</th>
            <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr class="border-t border-slate-100 odd:bg-white even:bg-slate-50/40">
                <td class="px-4 py-3">{{ $user->name }}</td>
                <td class="px-4 py-3">{{ $user->email }}</td>
                <td class="px-4 py-3"><x-status-badge :status="$user->role" /></td>
                <td class="px-4 py-3">
                    <a href="{{ route('users.show', $user) }}" class="btn-xs">Detail</a>
                    <a href="{{ route('users.edit', $user) }}" class="btn-xs">Edit</a>
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn-xs bg-rose-100 text-rose-700 hover:bg-rose-200" data-confirm="Hapus pengguna ini?">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
