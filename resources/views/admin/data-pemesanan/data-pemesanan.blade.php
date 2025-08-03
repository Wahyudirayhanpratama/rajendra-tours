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
                        <th class="text-center">No</th>
                        <th>No Tiket</th>
                        <th>Kode Booking</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Jumlah Penumpang</th>
                        <th>Rute</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemesanans as $i => $pemesanan)
                        <tr>
                            <td class="text-center">{{ $pemesanans->firstItem() + $i }}</td>
                            <td>{{ $pemesanan->tiket->no_tiket ?? '-' }}</td>
                            <td>{{ $pemesanan->kode_booking }}</td>
                            <td>Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $status = $pemesanan->status;
                                    $statusText = ucfirst(str_replace('_', ' ', $status));

                                    switch ($status) {
                                        case 'Lunas':
                                            $badgeClass = 'success';
                                            break;
                                        case 'belum_lunas':
                                            $badgeClass = 'warning';
                                            break;
                                        case 'Tiket dibatalkan':
                                            $badgeClass = 'danger';
                                            break;
                                        default:
                                            $badgeClass = 'secondary';
                                            break;
                                    }
                                @endphp

                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            <td>{{ $pemesanan->jumlah_penumpang }} Orang</td>
                            <td>{{ $pemesanan->jadwal->kota_asal }} - {{ $pemesanan->jadwal->kota_tujuan }}</td>
                            <td class="text-center">
                                <a href="{{ route('cetak.nota', $pemesanan->pemesanan_id) }}" target="_blank"
                                    class="btn btn-sm btn-pp text-white">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data pemesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3">
                {{ $pemesanans->links() }}
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
