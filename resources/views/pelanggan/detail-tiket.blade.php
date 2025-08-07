@extends('layouts.master4')

@section('title', 'Detail Tiket')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="appHeader bg-po">
            <div class="pageTitle text-white">TIKET SAYA</div>
        </div>

        <!-- Status -->
        <div class="text-end mt-5">
            @php
                $status = $pemesanan->status ?? 'unknown';

                switch ($status) {
                    case 'Lunas':
                        $badgeClass = 'bg-success text-white';
                        break;
                    case 'belum_lunas':
                        $badgeClass = 'bg-warning text-dark';
                        break;
                    case 'Tiket dibatalkan':
                        $badgeClass = 'bg-danger text-white';
                        break;
                    default:
                        $badgeClass = 'bg-secondary text-white';
                        break;
                }
            @endphp
            <span class="badge {{ $badgeClass }} fw-bold text-uppercase">
                {{ ucfirst(str_replace('_', ' ', $pemesanan->status)) }}
            </span>
        </div>

        <!-- Detail Card -->
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-body px-4 py-3">

                <div class="row g-3">
                    <div class="col-12">
                        <small class="text-muted">No Tiket</small><br>
                        <i class="bi bi-ticket-perforated me-2 fs-5 text-secondary"></i>
                        <span class="fw-bold">{{ $pemesanan->tiket->no_tiket ?? '-' }}</span>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Kode Booking</small><br>
                        <i class="bi bi-clipboard-check me-2 fs-5"></i>
                        <span class="fw-bold">{{ $pemesanan->kode_booking }}</span>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Rute</small><br>
                        <i class="bi bi-signpost me-2 fs-5"></i>
                        <span class="fw-bold">
                            {{ $pemesanan->jadwal->kota_asal ?? '?' }} -
                            {{ $pemesanan->jadwal->kota_tujuan ?? '?' }}
                        </span>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Jadwal</small><br>
                        <i class="bi bi-calendar-event me-2 fs-5"></i>
                        <span class="fw-bold">
                            {{ formatIndonesianDate($pemesanan->jadwal->tanggal) }},
                            {{ formatJam($pemesanan->jadwal->jam_berangkat) }} WIB
                        </span>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Harga</small><br>
                        <i class="bi bi-currency-dollar me-2 fs-5"></i>
                        <span class="fw-bold">
                            Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">No Kursi</small><br>
                        <i class="bi bi-person-bounding-box me-2 fs-5"></i>
                        <span class="fw-bold">{{ $pemesanan->tiket->nomor_kursi ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Syarat dan Ketentuan -->
        <div class="alert"
            style="border: 2px solid #000080; color: #000; font-size: 13px; padding: 10px; margin-top: 20px;">
            <span style="color: #b30000; font-weight: 700;">PERHATIAN</span><br>
            Syarat & ketentuan pemesanan yang berlaku:<br>
            1. Reservasi dan pembatalan dapat dilakukan 3 jam sebelum berangkat.<br>
            2. Jika melakukan pembatalan dilakukan setelah 3 jam sebelum berangkat, maka tidak ada pengembalian dana
            (NO REFUND & NO RESCHEDULE)
        </div>

        <!-- Action Buttons -->
        <div class="d-grid gap-2 mt-4">
            <a href="{{ route('tiket') }}" class="btn btn-outline-po fw-bold">Kembali</a>
            @if ($pemesanan->status !== 'Tiket dibatalkan')
                {{-- Tombol Bayar jika belum lunas --}}
                @if ($pemesanan->status !== 'Lunas' && $pemesanan->status !== 'lunas')
                    <a href="{{ route('bayar', ['id' => $pemesanan->pemesanan_id]) }}"
                        class="btn btn-warning fw-bold w-100">
                        Bayar Sekarang
                    </a>
                @endif
                <form action="{{ route('tiket.batalkan', $pemesanan->pemesanan_id) }}" method="POST"
                    class="form-batalkan">
                    @csrf
                    <button type="submit" class="btn btn-danger fw-bold w-100 btn-confirm-batalkan">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
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
    </style>
@endpush

@push('scriptspwa')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.form-batalkan');

            forms.forEach(function(form) {
                const button = form.querySelector('.btn-confirm-batalkan');
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Batalkan Tiket?',
                        text: 'Apakah kamu yakin ingin membatalkan pesanan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Batalkan!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
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
