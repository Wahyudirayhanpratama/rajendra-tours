<div class="appBottomMenu">
    <a href="{{ route('cari-jadwal') }}" class="item {{ request()->routeIs('cari-jadwal') ? 'active text-secondary' : '' }}">
        <div class="col {{ request()->routeIs('cari-jadwal') ? 'fw-bold' : 'text-dark' }}">
            <i class="uil uil-calendar-alt fs-20"></i>
            <strong>CARI JADWAL</strong>
        </div>
    </a>

    <a href="{{ route('tiket') }}" class="item {{ request()->routeIs('tiket') ? 'active text-secondary' : '' }}">
        <div class="col {{ request()->routeIs('tiket') ? 'fw-bold' : 'text-dark' }}">
            <i class="uil uil-ticket fs-20"></i>
            <strong>TIKET SAYA</strong>
        </div>
    </a>

    <a href="{{ route('riwayat') }}" class="item {{ request()->routeIs('riwayat') ? 'active text-secondary' : '' }}">
        <div class="col {{ request()->routeIs('riwayat') ? 'fw-bold' : 'text-dark' }}">
            <i class="uil uil-files-landscapes-alt fs-20"></i>
            <strong>RIWAYAT</strong>
        </div>
    </a>

    <a href="{{ route('profil') }}" class="item {{ request()->routeIs('profil') ? 'active text-secondary' : '' }}">
        <div class="col {{ request()->routeIs('profil') ? 'fw-bold' : 'text-dark' }}">
            <i class="uil uil-user-circle fs-20"></i>
            <strong>PROFIL</strong>
        </div>
    </a>
</div>
