@extends('layouts.master2')

@section('title', 'Laporan Transaksi')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Header -->
        <section class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 font-weight-bold">Laporan Transaksi</h1>
            </div>
        </section>

        <!-- Filter + Statistik -->
        <div class="container my-3">
            <div class="row mb-3">
                <form method="GET" action="{{ route('laporan.transaksi') }}" class="row mb-3">
                    <!-- Filter Bulan -->
                    <div class="col-md-2">
                        <label for="filterBulan">Bulan</label>
                        <select name="bulan" id="filterBulan" class="form-control" onchange="this.form.submit()">
                            @foreach (range(1, 12) as $b)
                                @php
                                    $bulanNum = str_pad($b, 2, '0', STR_PAD_LEFT); // '01', '02', dst
                                    $bulanName = formatIndonesia("2024-$bulanNum-01"); // Hasil: 'Januari 2024'
                                @endphp
                                <option value="{{ $bulanNum }}" {{ $bulan == $bulanNum ? 'selected' : '' }}>
                                    {{ explode(' ', $bulanName)[0] }} {{-- Ambil hanya nama bulannya --}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div class="col-md-2">
                        <label for="filterTahun">Tahun</label>
                        <select name="tahun" id="filterTahun" class="form-control" onchange="this.form.submit()">
                            @for ($t = 2023; $t <= now()->year; $t++)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Tiket Bulanan -->
                    <div class="col-md-4">
                        <label>Jumlah Tiket Perbulan</label>
                        <div class="form-control bg-light font-weight-bold">{{ $jumlahPerbulan }} Tiket</div>
                    </div>

                    <!-- Tiket Tahunan -->
                    <div class="col-md-4">
                        <label>Jumlah Tiket Pertahun</label>
                        <div class="form-control bg-light font-weight-bold">{{ $jumlahPertahun }} Tiket</div>
                    </div>
                </form>
            </div>

            <!-- Tabel Transaksi -->
            <table class="table table-bordered">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Nama Customer</th>
                        <th>Rute</th>
                        <th>Tanggal Berangkat</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemesanans as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->penumpang->nama ?? '-' }}</td>
                            <td>{{ $item->jadwal->kota_asal }} - {{ $item->jadwal->kota_tujuan }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->jadwal->tanggal)->format('d-m-Y') }}</td>
                            <td>Rp. {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('headers')
    <style>
        .container {
            max-width: 98%;
            overflow-x: auto;
            margin-left: 10px;
        }
    </style>
@endpush
