@extends('layouts.master8')

@section('title', 'Surat Jalan')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Surat Jalan</h1>
                </div>
            </div>
        </section>

        <div class="container">
            <a class="brand-link d-flex">
                <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo Rajendra" style="height: 60px">
            </a>
            <table class="no-border">
                <tr>
                    <td><strong>Tanggal Keberangkatan </strong></td>
                    <td><strong>:</strong></td>
                    <td>{{ formatIndonesianDate($jadwal->tanggal) }}</td>
                </tr>
                <tr>
                    <td><strong>Nomor Polisi </strong></td>
                    <td><strong>:</strong></td>
                    <td>{{ $jadwal->mobil->nomor_polisi }}</td>
                </tr>
                <tr>
                    <td><strong>Jam Keberangkatan </strong></td>
                    <td><strong>:</strong></td>
                    <td>{{ formatJam($jadwal->jam_berangkat) }} WIB</td>
                </tr>
                <tr>
                    <td><strong>Rute </strong></td>
                    <td><strong>:</strong></td>
                    <td>{{ $jadwal->kota_asal }} → {{ $jadwal->kota_tujuan }}</td>
                </tr>
            </table>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Nomor Kursi</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Alamat Jemput</th>
                        <th>Alamat Tujuan</th>
                        <th>Harga Tiket</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($jadwal->pemesanans as $pemesanan)
                        @foreach ($pemesanan->penumpangs as $i => $penumpang)
                            <tr>
                                <td>{{ $penumpang->nomor_kursi }}</td>
                                <td>{{ $penumpang->nama ?? '-' }}</td>
                                <td>{{ $penumpang->no_hp ?? '-' }}</td>
                                <td>{{ $penumpang->alamat_jemput }}</td>
                                <td>{{ $penumpang->alamat_antar }}</td>
                                <td>Rp{{ number_format($jadwal->harga, 0, ',', '.') }}</td>
                            </tr>
                            @php $total += $pemesanan->total_harga; @endphp
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total :</strong></td>
                        <td><strong>Rp{{ number_format($total, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="mr-4" style="margin-top: 100px; text-align: right;">
                <p>Tanda Tangan Sopir</p>
                <br><br>
                <p>______________________</p>
            </div>

            <div class="no-print">
                <button onclick="window.print()" class="btn btn-pp text-white shadow-sm">
                    <i class="fas fa-print mr-2"></i> Cetak Surat Jalan
                </button>
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
