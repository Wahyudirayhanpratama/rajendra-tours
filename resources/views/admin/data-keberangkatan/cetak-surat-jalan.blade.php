@extends('layouts.master8')

@section('title', 'Surat Jalan')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Surat Jalan</h1>
                    {{-- <a href="{{ route('tambah-data-pemesanan') }}" class="btn btn-tambah text-white">Tambah Pemesanan</a> --}}
                </div>
            </div>
        </section>

        <div class="container">
            <a href="" class="brand-link d-flex justify-content-center mt-4">
                <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo Rajendra">
            </a>
            <table class="no-border">
                <tr>
                    <td><strong>Tanggal Keberangkatan:</strong></td>
                    <td>{{ formatIndonesianDate($jadwal->tanggal) }}</td>
                </tr>
                <tr>
                    <td><strong>Nomor Polisi:</strong></td>
                    <td>{{ $jadwal->mobil->nomor_polisi }}</td>
                </tr>
                <tr>
                    <td><strong>Jam Keberangkatan:</strong></td>
                    <td>{{ formatJam($jadwal->jam_berangkat) }} WIB</td>
                </tr>
                <tr>
                    <td><strong>Rute:</strong></td>
                    <td>{{ $jadwal->kota_asal }} → {{ $jadwal->kota_tujuan }}</td>
                </tr>
            </table>

            <h4>Daftar Penumpang</h4>
            <table>
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
                                <td>{{ $penumpang->user->nama ?? '-' }}</td>
                                <td>{{ $penumpang->user->no_hp ?? '-' }}</td>
                                <td>{{ $penumpang->alamat_jemput }}</td>
                                <td>{{ $penumpang->alamat_tujuan }}</td>
                                <td>Rp{{ number_format($pemesanan->harga_tiket, 0, ',', '.') }}</td>
                            </tr>
                            @php $total += $pemesanan->harga_tiket; @endphp
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <table class="no-border">
                <tr>
                    <td><strong>Total Harga Tiket:</strong></td>
                    <td><strong>Rp{{ number_format($total, 0, ',', '.') }}</strong></td>
                </tr>
            </table>

            <div style="margin-top: 50px; text-align: right;">
                <p>Tanda Tangan Sopir</p>
                <br><br>
                <p>______________________</p>
            </div>

            <div class="no-print">
                <button onclick="window.print()">Cetak Surat Jalan</button>
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
