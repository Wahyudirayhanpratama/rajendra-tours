@extends('layouts.master')

@section('title', 'Jadwal Keberangkatan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Jadwal Keberangkatan</h1>
                    <a href="{{ route('tambah-jadwal-keberangkatan') }}" class="btn btn-tambah text-white">Tambah Jadwal</a>
                </div>
            </div>
        </section>

        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Polisi</th>
                        <th>Rute</th>
                        <th>Jam Berangkat</th>
                        <th>Tanggal Berangkat</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwals as $index => $jadwal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $jadwal->mobil->nomor_polisi }}</td>
                            <td>{{ $jadwal->kota_asal }} - {{ $jadwal->kota_tujuan }}</td>
                            <td>{{ \Carbon\Carbon::parse($jadwal->jam_berangkat)->format('H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d-m-Y') }}</td>
                            <td>Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('edit-jadwal-keberangkatan', $jadwal->jadwal_id) }}"
                                    class="btn btn-sm btn-pp text-white">Edit</a>
                                <form action="{{ route('hapus-jadwal-keberangkatan', $jadwal->jadwal_id) }}" method="POST"
                                    style="display:inline;" data-confirm="true">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Data jadwal kosong</td>
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
