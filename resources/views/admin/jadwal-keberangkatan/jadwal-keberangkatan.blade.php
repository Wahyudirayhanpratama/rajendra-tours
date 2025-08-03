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
                        <th class="text-center">No</th>
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
                            <td class="text-center">{{ $jadwals->firstItem() + $index }}</td>
                            <td>{{ $jadwal->mobil->nomor_polisi }}</td>
                            <td>{{ $jadwal->kota_asal }} - {{ $jadwal->kota_tujuan }}</td>
                            <td>{{ formatJam($jadwal->jam_berangkat) }} WIB</td>
                            <td>{{ formatIndonesianDate($jadwal->tanggal) }}</td>
                            <td>Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('edit-jadwal-keberangkatan', $jadwal->jadwal_id) }}"
                                    class="btn btn-sm btn-pp text-white"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('hapus-jadwal-keberangkatan', $jadwal->jadwal_id) }}" method="POST"
                                    style="display:inline;" data-confirm="true">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
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
            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3">
                {{ $jadwals->links() }}
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
