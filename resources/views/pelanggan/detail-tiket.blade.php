@extends('layouts.master4')

@section('title', 'Detail Tiket')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="appHeader bg-po">
            <div class="pageTitle text-white">TIKET SAYA</div>
        </div>

        <!-- Status -->
        <div class="d-flex justify-content-end mt-5 mb-3">
            @php
                $badgeClass =
                    $pemesanan->status === 'Tiket dibatalkan'
                        ? 'bg-danger text-white'
                        : 'bg-success bg-opacity-25 text-success';
            @endphp
            <span class="badge {{ $badgeClass }} px-5 py-2 fw-semibold">
                {{ ucfirst(str_replace('_', ' ', $pemesanan->status)) }}
            </span>
        </div>

        <!-- Detail Info -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-4">Informasi Tiket</h5>
                <div class="mb-3 d-flex align-items-start">
                    <i class="bi bi-ticket-perforated me-2 fs-5 text-secondary"></i>
                    <div class="flex-grow-1 d-flex justify-content-between">
                        <strong>No Tiket</strong>
                        <strong>{{ $pemesanan->tiket->no_tiket ?? '-' }}</strong>
                    </div>
                </div>

                <div class="mb-2 d-flex align-items-start">
                    <i class="bi bi-clipboard-check me-2 fs-5"></i>
                    <div class="flex-grow-1 d-flex justify-content-between">
                        <span>Kode Booking</span>
                        <strong>{{ $pemesanan->kode_booking }}</strong>
                    </div>
                </div>

                <div class="mb-2 d-flex align-items-start">
                    <i class="bi bi-signpost me-2 fs-5"></i>
                    <div class="flex-grow-1 d-flex justify-content-between">
                        <span>Rute</span>
                        <strong>{{ $pemesanan->jadwal->kota_asal ?? '?' }} -
                            {{ $pemesanan->jadwal->kota_tujuan ?? '?' }}</strong>
                    </div>
                </div>

                <div class="mb-2 d-flex align-items-start">
                    <i class="bi bi-calendar-event me-2 fs-5"></i>
                    <div class="flex-grow-1 d-flex justify-content-between">
                        <span>Jadwal</span>
                        <strong>
                            {{ formatIndonesianDate($pemesanan->jadwal->tanggal) }},
                            {{ formatJam($pemesanan->jadwal->jam_berangkat) }} WIB
                        </strong>
                    </div>
                </div>

                <div class="mb-2 d-flex align-items-start">
                    <i class="bi bi-currency-dollar me-2 fs-5"></i>
                    <div class="flex-grow-1 d-flex justify-content-between">
                        <span>Harga</span>
                        <strong>Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-start">
                    <i class="bi bi-person-bounding-box me-2 fs-5"></i>
                    <div class="flex-grow-1 d-flex justify-content-between">
                        <span>No Kursi</span>
                        <strong>{{ $pemesanan->tiket->nomor_kursi ?? '-' }}</strong>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-grid gap-2 mx-3 btn-fix">
                    @if ($pemesanan->status !== 'Tiket dibatalkan')
                        <form action="{{ route('tiket.batalkan', $pemesanan->pemesanan_id) }}" method="POST"
                            class="form-batalkan d-inline">
                            @csrf
                            <button type="submit" class="btn btn-po fw-bold w-100 btn-confirm-batalkan">Batalkan
                                Pesanan</button>
                        </form>
                    @endif
                    <a href="{{ route('tiket') }}" class="btn btn-outline-po fw-bold">Kembali</a>
                </div>
            </div>
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
@endpush
