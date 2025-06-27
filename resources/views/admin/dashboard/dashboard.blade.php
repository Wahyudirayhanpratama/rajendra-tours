@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('storage/logo_jendra.png') }}" alt="Logo Rajendra" height="200" width="200">
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
                        <div class="col-sm-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $jumlahPelanggan }}</h3>
                                    <p>Jumlah Customer</p>
                                    <p style="font-size: 14px;">dari Total di bulan {{ formatIndonesia(date('F Y')) }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users fa-3x" style="color: white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $pemesananBulanIni }}</h3>
                                    <p>Jumlah Pemesanan</p>
                                    <p style="font-size: 14px;">dari Total di bulan {{ formatIndonesia(date('F Y')) }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list fa-3x" style="color: white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small-box bg-blue">
                                <div class="inner">
                                    <h3>{{ $jumlahMobil }}</h3>
                                    <p>Jumlah Unit</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-car fa-3x" style="color: white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small-box bg-blue">
                                <div class="inner">
                                    <h3>{{ $pembatalanBulanIni }}</h3>
                                    <p>Total Pembatalan Tiket di Bulan {{ formatIndonesia(date('F Y')) }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line fa-3x" style="color: white"></i>
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
                </div><!-- /.container-fluid -->
            </section>
        </div>
        <!-- /.content-wrapper -->
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
@endsection
