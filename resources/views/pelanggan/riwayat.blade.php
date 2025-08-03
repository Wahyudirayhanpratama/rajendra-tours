@extends('layouts.master5')

@section('title', 'Riwayat Pesanan')

@section('content')

    <!-- Header -->
    <div class="appHeader bg-po">
        <div class="left">
            <a href="#" class="headerButton goBack">
                <i class="uil uil-angle-left-b fs-25 text-white"></i>
            </a>
        </div>
        <div class="pageTitle text-white">RIWAYAT PESANAN</div>
    </div>

    <!-- Konten -->
    <div id="appCapsule" class="full-height p-y">

        @forelse($riwayatTiket as $pemesanan)
            <!-- Tanggal -->
            <div class="card-body background-light">
                <p class="text-tanggal mt-1 mb-1 py-2">
                    {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal)->translatedFormat('d F Y') }}</p>
            </div>

            <!-- Card Riwayat -->
            <div class="card mt-2 mb-2 mx-3" style="border-radius: 12px;">
                <div class="card-body p-0">

                    <!-- Header abu-abu (Kode Booking + Status) -->
                    <div class="bg-riwayat d-flex justify-content-between align-items-start px-3 py-2"
                        style="border-top-left-radius: 10px; border-top-right-radius: 10px; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailModal-{{ $loop->index }}">
                        <div>
                            <p class="mb-1 text-secondary fw-semibold" style="font-size: 0.9rem;">Kode Booking</p>
                            <p class="fw-bold mb-0" style="font-size: 1.1rem;">{{ $pemesanan->kode_booking }}</p>
                        </div>
                        <span
                            class="badge {{ $pemesanan->status === 'Tiket dibatalkan' ? 'bg-danger text-white' : 'bg-success text-white' }}"
                            style="border-radius: 10px; padding: 5px 10px; font-size: 0.8rem; margin-top: 5px;">
                            {{ ucfirst(str_replace('_', ' ', $pemesanan->status)) }}
                        </span>
                    </div>

                    <!-- Body Putih (Informasi Pemesanan) -->
                    <div class="px-3 py-3"
                        style="background-color: white; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailModal-{{ $loop->index }}">
                        <p class="mb-2 fw-bold" style="font-size: 1rem;">{{ $pemesanan->jadwal->kota_asal }} –
                            {{ $pemesanan->jadwal->kota_tujuan }}<br><span class="fw-normal">Jam
                                {{ formatJam($pemesanan->jadwal->jam_berangkat) }} –
                                {{ $pemesanan->jadwal->kota_asal }}</span></p>

                        <p class="mb-2 fw-semibold" style="font-size: 0.95rem;">
                            {{ $pemesanan->jadwal->mobil->nama_mobil }}<br>
                            <span class="text-secondary">{{ $pemesanan->jadwal->mobil->nomor_polisi }}</span>
                        </p>

                        <div class="text-end">
                            <p class="mb-0 text-muted">Total Harga</p>
                            <p class="fw-bold text-po" style="font-size: 1.1rem;">Rp.
                                {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal (Popup Detail) -->
            <div class="modal fade modal-bottom" id="detailModal-{{ $loop->index }}" tabindex="-1"
                aria-labelledby="detailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content rounded-top-4">
                        <!-- Header dengan Logo dan Tombol Silang -->
                        <div class="d-flex justify-content-between align-items-center px-3 pt-3">
                            <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo" style="max-width: 150px;" />
                            <a href="#" data-bs-dismiss="modal">
                                <i class="uil uil-multiply fs-18 text-dark"></i>
                            </a>
                        </div>

                        <div class="modal-body">
                            <!-- Isi Modal -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="fw-medium">Nomor Tiket</p>
                                {{-- <div class="text-end ms-auto"> --}}
                                <p class="fw-bold">{{ $pemesanan->tiket->no_tiket }}</p>
                                {{-- </div> --}}
                            </div>

                            <!-- Status -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0">Status</p>
                                <span
                                    class="badge {{ $pemesanan->status === 'Tiket dibatalkan' ? 'bg-danger text-white' : 'bg-success text-white' }}"
                                    style="background-color: #b4ffa8; font-size: 0.85rem;">{{ ucfirst(str_replace('_', ' ', $pemesanan->status)) }}</span>
                            </div>

                            <!-- Kode Booking -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0">Kode Booking</p>
                                <p class="fw-medium mb-0">{{ $pemesanan->kode_booking }}</p>
                            </div>

                            <!-- Nomor Transaksi -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0">Nomor Transaksi</p>
                                <p class="fw-medium mb-0">
                                    {{ substr(strtoupper(str_replace('-', '', $pemesanan->transaction_id)), 0, 16) }}</p>
                            </div>

                            <!-- Tanggal Transaksi -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0">Tanggal Transaksi</p>
                                <p class="fw-medium mb-0">{{ $pemesanan->transaction_time }}</p>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0">Metode Pembayaran</p>
                                <p class="fw-medium mb-0">{{ ucfirst(str_replace('_', ' ', $pemesanan->payment_type)) }}
                                </p>
                            </div>

                            <!-- Total -->
                            <div class="border rounded px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0 fw-semibold">Total</p>
                                    <p class="mb-0 fw-bold text-po">Rp.
                                        {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted alert alert-secondary mt-3 mx-2">
                Belum ada riwayat tiket.
            </div>
        @endforelse
    </div>
@endsection

@push('headerspwa')
    <style>
        .loginbg {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            width: 100%;
            height: 25vh;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .city-schedule {
            padding: 0 8px;
            z-index: 1;
            position: relative;
        }

        .line-dot {
            position: absolute;
            border-bottom: 2px dotted #ffffff;
            top: 0;
            width: -webkit-fill-available;
            bottom: 26px;
            margin: 0px 20px;
        }

        .bg-po {
            background-color: #000080 !important;
        }

        .bg-blue {
            background-color: #92B6F8 !important
        }

        .btn-fix {
            margin-top: 150px;
        }

        .text-tanggal {
            font-size: 13px;
            margin-left: 20px;
        }

        .bg-riwayat {
            background-color: #ECECEC !important;
        }

        .modal.modal-bottom .modal-dialog {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0;
            max-width: 100%;
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
        }

        .modal.show.modal-bottom .modal-dialog {
            transform: translateY(0);
        }

        .modal.modal-bottom .modal-content {
            border-radius: 20px 20px 0 0;
            padding-bottom: 20px;
        }
    </style>
@endpush

@push('scriptspwa')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.log('ServiceWorker registration failed:', error);
                });
        }
    </script>
@endpush
