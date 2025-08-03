@extends('layouts.master')

@section('title', 'Data Keberangkatan')

@section('content')
    <div class="content-wrapper">
        <!-- Header Halaman -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Daftar Unit yang Berangkat</h1>
                </div>
            </div>
        </section>

        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>No Polisi</th>
                        <th>Rute</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Jumlah Penumpang</th>
                        <th>Jam Berangkat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalHariIni as $jadwal)
                        @php
                            $jadwalTime = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam_berangkat);
                            $status = now()->gt($jadwalTime) ? 'Sudah Berangkat' : 'Belum Berangkat';
                            $jumlahPenumpang = $jadwal->pemesanans->sum('jumlah_penumpang');
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $jadwal->mobil->nomor_polisi }}</td>
                            <td>{{ $jadwal->kota_asal }} - {{ $jadwal->kota_tujuan }}</td>
                            <td>{{ formatIndonesianDate($jadwal->tanggal) }}</td>
                            <td>
                                <span class="badge {{ $status === 'Belum Berangkat' ? 'bg-warning' : 'bg-success' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td>{{ $jumlahPenumpang }}</td>
                            <td>{{ formatJam($jadwal->jam_berangkat) }} WIB</td>
                            <td class="text-center">
                                <a href="{{ route('cetak.surat-jalan', $jadwal->jadwal_id) }}" target="_blank"
                                    class="btn btn-sm btn-pp text-white">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada jadwal keberangkatan hari
                                ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3">
                {{ $jadwalHariIni->links() }}
            </div>
        </div>
        <!-- /.card-body -->
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
