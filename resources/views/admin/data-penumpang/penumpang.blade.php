@extends('layouts.master')

@section('title', 'Data Penumpang')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Data Pemesanan</h1>
                    <a href="{{ route('tambah-data-penumpang') }}" class="btn btn-tambah text-white">Tambah Penumpang</a>
                </div>
            </div>
        </section>

        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No Telp</th>
                        <th>Jenis Kelamin</th>
                        <th>Tujuan</th>
                        <th>No Polisi</th>
                        <th>Alamat Jemput</th>
                        <th>Alamat Antar</th>
                        <th>Kode Booking</th>
                        <th>Tanggal Berangkat</th>
                        <th>Jumlah Penumpang</th>
                        <th>Nomor Kursi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penumpangs as $i => $penumpang)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $penumpang->nama }}</td>
                            <td>{{ $penumpang->no_hp }}</td>
                            <td>{{ ucfirst($penumpang->jenis_kelamin) }}</td>
                            <td>{{ $penumpang->pemesanan->jadwal->kota_tujuan ?? '-' }}</td>
                            <td>{{ $penumpang->pemesanan->jadwal->mobil->nomor_polisi ?? '-' }}</td>
                            <td>{{ $penumpang->alamat_jemput }}</td>
                            <td>{{ $penumpang->alamat_antar }}</td>
                            <td>{{ $penumpang->pemesanan->kode_booking ?? '-' }}</td>
                            <td>{{ formatIndonesianDate($penumpang->pemesanan->jadwal->tanggal ?? '-') }} -
                                {{ formatJam($penumpang->pemesanan->jadwal->jam_berangkat ?? '-') }}</td>
                            <td>{{ $penumpang->pemesanan->jumlah_penumpang }}</td>
                            <td>{{ $penumpang->nomor_kursi }}</td>
                            <td>
                                <a href="{{ route('edit-data-penumpang', $penumpang->penumpang_id) }}"
                                    class="btn btn-sm btn-pp text-white mb-1">Edit</a>

                                <form action="{{ route('hapus-data-penumpang', $penumpang->penumpang_id) }}" method="POST"
                                    style="display:inline;" data-confirm="true">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center">Tidak ada data pemesanan.</td>
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
