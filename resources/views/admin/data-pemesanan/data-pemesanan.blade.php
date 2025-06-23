@extends('layouts.master')

@section('title', 'Data Pemesanan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Data Pemesanan</h1>
                    {{-- <a href="{{ route('tambah-data-pemesanan') }}" class="btn btn-tambah text-white">Tambah Pemesanan</a> --}}
                </div>
            </div>
        </section>

        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Tiket</th>
                        <th>Kode Booking</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Jumlah Penumpang</th>
                        <th>Rute</th>
                        {{-- <th>Aksi</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemesanans as $i => $pemesanan)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $pemesanan->tiket->no_tiket ?? '-' }}</td>
                            <td>{{ $pemesanan->kode_booking }}</td>
                            <td>Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $pemesanan->status)) }}</td>
                            <td>{{ $pemesanan->jumlah_penumpang }}</td>
                            <td>{{ $pemesanan->jadwal->kota_asal }} - {{ $pemesanan->jadwal->kota_tujuan }}</td>
                            {{-- <td>
                                <a href="{{ route('edit-data-pemesanan', $pemesanan->pemesanan_id) }}"
                                    class="btn btn-sm btn-pp text-white">Edit</a>
                                <form action="{{ route('hapus-data-pemesanan', $pemesanan->pemesanan_id) }}" method="POST"
                                    style="display:inline;" data-confirm="true">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data pemesanan.</td>
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
