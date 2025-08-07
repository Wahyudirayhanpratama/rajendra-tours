@extends('layouts.master8')

@section('title', 'Nota Pemesanan')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center mb-3">
                <h1 class="m-0 font-weight-bold">Nota Pemesanan</h1>
                <div class="d-flex gap-2 d-print-none">
                    <a href="{{ route('data-pemesanan') }}" class="btn btn-secondary mr-2">
                        Kembali
                    </a>
                    <button onclick="window.print()" class="btn btn-pp text-white">
                        <i class="fas fa-print mr-2"></i> Cetak
                    </button>
                </div>
            </div>
        </section>

        <div class="container">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo Rajendra" style="height: 60px;">
                <div class="text-end text-dark">
                    <small class="text-secondary">Kode Booking</small>
                    <h5 class="fw-bold mb-0">{{ $pemesanan->kode_booking }}</h5>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="no-border">
                        <tr>
                            <td><strong>Nomor Tiket </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ $pemesanan->tiket->no_tiket ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nomor Transaksi </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ strtoupper(str_replace('-', '', $pemesanan->transaction_id)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Keberangkatan </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ formatIndonesianDate($pemesanan->jadwal->tanggal) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Penumpang </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ $pemesanan->jumlah_penumpang }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jam Keberangkatan </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ formatJam($pemesanan->jadwal->jam_berangkat) }} WIB</td>
                        </tr>
                        <tr>
                            <td><strong>Nomor Polisi </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ $pemesanan->jadwal->mobil->nomor_polisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Rute </strong></td>
                            <td><strong>:</strong></td>
                            <td>{{ $pemesanan->jadwal->kota_asal }} →
                                {{ $pemesanan->jadwal->kota_tujuan }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light border rounded mb-2">
                        <div><strong>Harga Tiket:</strong></div>
                        <div class="h5 mb-0">Rp{{ number_format($pemesanan->jadwal->harga ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-success text-white border rounded mb-2">
                        <div><strong>Total Bayar:</strong></div>
                        <div class="h5 mb-0">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="mb-3 mt-2">
                            <h6 class="fw-bold">Detail Penumpang</h6>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>No HP</th>
                                        <th>No Kursi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pemesanan->penumpangs as $penumpang)
                                        <tr>
                                            <td>{{ $penumpang->nama ?? '-' }}</td>
                                            <td>{{ $penumpang->no_hp ?? '-' }}</td>
                                            <td>{{ $penumpang->nomor_kursi ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
