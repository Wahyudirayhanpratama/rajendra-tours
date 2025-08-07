@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('storage/logo_jendra.png') }}" alt="Logo Rajendra" height="200"
            width="200">
    </div>
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="m-0 font-weight-bold">Dashboard</h1>
                    </div>
                </div>
            </section>

            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $jumlahPelanggan }}</h3>
                                    <p>Jumlah Customer</p>
                                    <p>dari Total di bulan {{ formatIndonesia(date('F Y')) }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $pemesananBulanIni }}</h3>
                                    <p>Jumlah Pemesanan</p>
                                    <p>dari Total di bulan {{ formatIndonesia(date('F Y')) }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $pembatalanBulanIni }}</h3>
                                    <p>Jumlah Pembatalan Tiket</p>
                                    <p>dari Total di bulan {{ formatIndonesia(date('F Y')) }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $jumlahMobil }}</h3>
                                    <p>Jumlah Mobil</p>
                                    <p>Pada Saat Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-car"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong>Daftar Unit yang Berangkat</strong></h3>

                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right"
                                        placeholder="Search">

                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No Polisi</th>
                                        <th>Rute</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Jumlah Penumpang</th>
                                        <th>Jam Berangkat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwalHariIni as $jadwal)
                                        @php
                                            $jamBerangkat = formatJam($jadwal->jam_berangkat);
                                            $status = now()->lessThan($jamBerangkat)
                                                ? 'Belum Berangkat'
                                                : 'Sudah Berangkat';
                                            $jumlahPenumpang = $jadwal->pemesanans->sum('jumlah_penumpang');
                                        @endphp
                                        <tr>
                                            <td>{{ $jadwal->mobil->nomor_polisi }}</td>
                                            <td>{{ $jadwal->kota_asal }} - {{ $jadwal->kota_tujuan }}</td>
                                            <td>{{ formatIndonesianDate($jadwal->tanggal) }}
                                            </td>
                                            <td>
                                                @php
                                                    $jadwalTime = \Carbon\Carbon::parse(
                                                        $jadwal->tanggal . ' ' . $jadwal->jam_berangkat,
                                                    );
                                                    $now = now();
                                                    $status = $now->gt($jadwalTime)
                                                        ? 'Sudah Berangkat'
                                                        : 'Belum Berangkat';
                                                @endphp
                                                <span
                                                    class="badge {{ $status === 'Belum Berangkat' ? 'bg-warning' : 'bg-success' }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td>{{ $jadwal->pemesanan ? $jadwal->pemesanan->sum('jumlah_penumpang') : 0 }}
                                            </td>
                                            <td>{{ formatJam($jadwal->jam_berangkat) }} WIB</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Tidak ada jadwal keberangkatan
                                                hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong>Daftar Pelanggan yang Berangkat</strong></h3>

                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right"
                                        placeholder="Search">

                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <tbody>
                                    @forelse ($jadwalHariIni as $jadwal)
                                        @if ($jadwal->pemesanans->count())
                                            <tr class="bg-dark text-white text-center">
                                                <td colspan="6" class="font-weight-bold">
                                                    {{ $jadwal->mobil->nama_mobil ?? 'Tidak diketahui' }} -
                                                    <span
                                                        class="text-primary">{{ $jadwal->mobil->nomor_polisi ?? 'N/A' }}</span>
                                                    - {{ $jadwal->kota_tujuan }}
                                                </td>
                                            </tr>

                                            {{-- Ulangi header di setiap jadwal --}}
                                            <tr class="table-active">
                                                <th>Nama</th>
                                                <th>Tanggal Berangkat</th>
                                                <th>Status Pembayaran</th>
                                                <th>Nomor Kursi</th>
                                                <th>Nomor Hp</th>
                                            </tr>

                                            @foreach ($jadwal->pemesanans as $pemesanan)
                                                @php
                                                    $statusPembayaran = $pemesanan->status ?? 'unknown';

                                                    switch ($statusPembayaran) {
                                                        case 'Lunas':
                                                            $badge = 'bg-success';
                                                            break;
                                                        case 'belum_lunas':
                                                            $badge = 'bg-warning';
                                                            break;
                                                        case 'Tiket dibatalkan':
                                                            $badge = 'bg-danger';
                                                            break;
                                                        default:
                                                            $badge = 'bg-secondary';
                                                            break;
                                                    }

                                                    $labelStatus = match ($statusPembayaran) {
                                                        'lunas' => 'Lunas',
                                                        'belum_lunas' => 'Menunggu Pembayaran',
                                                        'Tiket dibatalkan' => 'Tiket Dibatalkan',
                                                        default => ucfirst(str_replace('_', ' ', $statusPembayaran)),
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>{{ $pemesanan->penumpang->nama ?? '-' }}</td>
                                                    <td>{{ formatIndonesianDate($jadwal->tanggal) }}</td>
                                                    <td><span class="badge {{ $badge }}">{{ $labelStatus }}</span>
                                                    </td>
                                                    <td>{{ $pemesanan->penumpang->nomor_kursi }}</td>
                                                    <td>{{ $pemesanan->penumpang->no_hp }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Tidak ada jadwal keberangkatan
                                                hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    </div>
    <!-- ./wrapper -->
@endsection
