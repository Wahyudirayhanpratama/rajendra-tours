<!-- Main Sidebar Container -->
<aside class="main-sidebar bg-blue">
    <!-- Brand Logo -->
    <a href="" class="brand-link d-flex justify-content-center mt-4">
        <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo Rajendra">
    </a>

    <div class="border-bottom mx-2 my-2"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-5">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @php
                    $guard = Auth::guard('admin')->check()
                        ? 'admin'
                        : (Auth::guard('pemilik')->check()
                            ? 'pemilik'
                            : null);
                @endphp
                @if ($guard === 'admin')
                    <!-- Menu Admin -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard.admin') }}"
                            class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
                            <i class="side-icon fa-solid fa-house"></i>
                            <p class="text-white">Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('data-mobil') }}"
                            class="nav-link {{ request()->routeIs('data-mobil') ? 'active' : '' }}">
                            <i class="side-icon fa-solid fa-car"></i>
                            <p class="text-white">Data Mobil</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jadwal-keberangkatan') }}"
                            class="nav-link {{ request()->routeIs('jadwal-keberangkatan') ? 'active' : '' }}">
                            <i class="side-icon fa-solid fa-calendar-days"></i>
                            <p class="text-white">Jadwal Keberangkatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('data.penumpang') }}"
                            class="nav-link {{ request()->routeIs('data.penumpang') ? 'active' : '' }}">
                            <i class="side-icon bi bi-people-fill"></i>
                            <p class="text-white">Data Penumpang</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('data-pemesanan') }}"
                            class="nav-link {{ request()->routeIs('data-pemesanan') ? 'active' : '' }}">
                            <i class="side-icon bi bi-clipboard-fill"></i>
                            <p class="text-white">Data Pemesanan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('data-pelanggan') }}"
                        class="nav-link {{ request()->routeIs('data-pelanggan') ? 'active' : '' }}">
                        <i class="side-icon bi bi-person-plus-fill"></i>
                        <p class="text-white">Data Akun</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('surat.jalan') }}"
                        class="nav-link {{ request()->routeIs('surat.jalan') ? 'active' : '' }}">
                        <i class="side-icon bi bi-clipboard2-fill"></i>
                        <p class="text-white">Surat Jalan</p>
                    </a>
                </li>
                @elseif ($guard === 'pemilik')
                    <!-- Menu Pemilik -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard.pemilik') }}"
                            class="nav-link {{ request()->routeIs('dashboard.pemilik') ? 'active' : '' }}">
                            <i class="side-icon fa-solid fa-house"></i>
                            <p class="text-white">Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('laporan.transaksi') }}"
                            class="nav-link {{ request()->routeIs('laporan.transaksi') ? 'active' : '' }}">
                            <i class="side-icon fa-solid fa-car"></i>
                            <p class="text-white">Laporan Transaksi</p>
                        </a>
                    </li>
                @endif

                <!-- Logout (Tampil untuk semua role) -->
                <li class="nav-item mt-auto">
                    <a href="#" data-confirm="logout">
                        <i class="side-icon fa-solid fa-right-from-bracket text-white"></i>
                        <p class="text-white">Logout</p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

@stack('sidebar')
