<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pemesanan Kendaraan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <div class="topbar-inner">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Fleet Booking System</p>
                <p class="text-sm font-semibold text-slate-900">Monitoring Kendaraan Operasional Tambang</p>
            </div>

            @auth
                <div class="text-right text-xs text-slate-500">
                    <p>{{ auth()->user()->name }}</p>
                    <p class="uppercase tracking-wide">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            @endauth
        </div>
    </header>

    <div class="@auth app-main @else mx-auto grid h-full w-full max-w-360 gap-4 px-4 py-4 lg:px-6 @endauth">
        @auth
            <aside class="sidebar">
                <div class="nav-section">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="nav-section">
                        <p class="nav-label">Operasional</p>
                        <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'nav-link-active' : '' }}">Pemesanan</a>
                        <a href="{{ route('vehicle-usages.index') }}" class="nav-link {{ request()->routeIs('vehicle-usages.*') ? 'nav-link-active' : '' }}">Pemakaian</a>
                        <a href="{{ route('fuel-consumptions.index') }}" class="nav-link {{ request()->routeIs('fuel-consumptions.*') ? 'nav-link-active' : '' }}">BBM</a>
                        <a href="{{ route('vehicle-services.index') }}" class="nav-link {{ request()->routeIs('vehicle-services.*') ? 'nav-link-active' : '' }}">Servis</a>
                    </div>

                    <div class="nav-section">
                        <p class="nav-label">Master Data</p>
                        <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'nav-link-active' : '' }}">Kendaraan</a>
                        <a href="{{ route('drivers.index') }}" class="nav-link {{ request()->routeIs('drivers.*') ? 'nav-link-active' : '' }}">Pengemudi</a>
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'nav-link-active' : '' }}">Pengguna</a>
                    </div>

                    <div class="nav-section">
                        <p class="nav-label">Audit</p>
                        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'nav-link-active' : '' }}">Log Aktivitas</a>
                    </div>
                @endif

                @if(auth()->user()->role === 'approver_l1' || auth()->user()->role === 'approver_l2')
                    <div class="nav-section">
                        <p class="nav-label">Approval</p>
                        <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'nav-link-active' : '' }}">Persetujuan</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-slate-100 pt-4">
                    @csrf
                    <button class="btn-danger w-full" data-confirm="Keluar dari aplikasi sekarang?" type="submit">Keluar</button>
                </form>
            </aside>
        @endauth

        <main class="content-pane">
            @auth
                <div class="mobile-nav mb-3">
                    <details>
                        <summary class="btn-soft w-full cursor-pointer">Menu Navigasi</summary>
                        <div class="mobile-nav-list">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'nav-link-active' : '' }}">Pemesanan</a>
                                <a href="{{ route('vehicle-usages.index') }}" class="nav-link {{ request()->routeIs('vehicle-usages.*') ? 'nav-link-active' : '' }}">Pemakaian</a>
                                <a href="{{ route('fuel-consumptions.index') }}" class="nav-link {{ request()->routeIs('fuel-consumptions.*') ? 'nav-link-active' : '' }}">BBM</a>
                                <a href="{{ route('vehicle-services.index') }}" class="nav-link {{ request()->routeIs('vehicle-services.*') ? 'nav-link-active' : '' }}">Servis</a>
                                <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'nav-link-active' : '' }}">Kendaraan</a>
                                <a href="{{ route('drivers.index') }}" class="nav-link {{ request()->routeIs('drivers.*') ? 'nav-link-active' : '' }}">Pengemudi</a>
                                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'nav-link-active' : '' }}">Pengguna</a>
                                <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'nav-link-active' : '' }}">Log Aktivitas</a>
                            @endif
                            @if(auth()->user()->role === 'approver_l1' || auth()->user()->role === 'approver_l2')
                                <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'nav-link-active' : '' }}">Persetujuan</a>
                            @endif
                        </div>
                    </details>
                </div>
            @endauth

            @if (session('status'))
                <div class="toast-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="toast-error">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<div id="loadingOverlay" class="loading-overlay" aria-hidden="true">
    <div class="loading-box">
        <div class="spinner" aria-hidden="true"></div>
        <p class="text-sm font-medium text-slate-700">Memproses permintaan...</p>
    </div>
</div>

<div id="confirmModal" class="modal-backdrop" aria-hidden="true">
    <div class="modal-card">
        <h2 class="mb-2">Konfirmasi Aksi</h2>
        <p id="confirmMessage" class="mb-4 text-sm text-slate-600">Apakah Anda yakin?</p>
        <div class="flex justify-end gap-2">
            <button id="confirmCancel" type="button" class="btn-soft">Batal</button>
            <button id="confirmOk" type="button" class="btn-danger">Lanjutkan</button>
        </div>
    </div>
</div>
</body>
</html>
