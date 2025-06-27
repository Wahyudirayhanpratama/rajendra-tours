<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light d-flex justify-content-between px-3">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Left: Dashboard Title -->
    <div class="navbar-brand font-weight-bold mb-0"></div>

    <!-- Right: Admin Greeting -->
    @php
        $user = Auth::guard('admin')->user() ?? Auth::guard('pemilik')->user();
        $greeting = $user?->role ?? 'Pengguna';
    @endphp

    <div class="d-flex align-items-center">
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="User Icon" width="30" height="30"
            class="mx-2">
        <span>Hi, {{ ucfirst($greeting) }}!</span>
    </div>
</nav>
<!-- /.navbar -->

@stack('navbars')
