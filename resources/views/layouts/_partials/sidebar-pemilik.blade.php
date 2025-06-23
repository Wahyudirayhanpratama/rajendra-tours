<!-- Main Sidebar Container -->
<aside class="main-sidebar bg-blue">
    <!-- Brand Logo -->
    <a href="" class="brand-link d-flex justify-content-center mt-4">
        <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo Rajendra">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-5">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('dashboard-pemilik') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="side-icon fa-solid fa-house"></i>
                        <p class="text-white">
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan-transaksi') }}"
                        class="nav-link {{ request()->routeIs('laporan-transaksi') ? 'active' : '' }}">
                        <i class="side-icon fa-solid fa-car"></i>
                        <p class="text-white">
                            Laporan Transaksi
                        </p>
                    </a>
                </li>
                <!-- Logout -->
                <li class="nav-item fixed-bottom mb-3 mx-3">
                    <a href="#" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="side-icon fa-solid fa-right-from-bracket text-white"></i>
                        <p class="text-white">Logout</p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

@stack('sidebar')
