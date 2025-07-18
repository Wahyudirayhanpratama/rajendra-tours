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
                $badgeClass =
                    $pemesanan->status === 'Tiket dibatalkan'
                        ? 'bg-danger text-white'
                        : 'bg-success bg-opacity-25 text-success';
            @endphp
            <span class="badge {{ $badgeClass }} px-4 py-2 fw-semibold">
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

        <!-- Action Buttons -->
        <div class="d-grid gap-2 mt-4">
            @if ($pemesanan->status !== 'Tiket dibatalkan')
                <form action="{{ route('tiket.batalkan', $pemesanan->pemesanan_id) }}" method="POST"
                    class="form-batalkan">
                    @csrf
                    <button type="submit" class="btn btn-po fw-bold w-100 btn-confirm-batalkan">
                        Batalkan Pesanan
                    </button>
                </form>
                {{-- Tombol Bayar jika belum lunas --}}
                @if (!$pemesanan->pembayaran || $pemesanan->pembayaran->status !== 'lunas')
                    <a href="{{ route('bayar', ['id' => $pemesanan->pemesanan_id]) }}"
                        class="btn btn-po fw-bold w-100">
                        Bayar Sekarang
                    </a>
                @endif
            @endif
            <a href="{{ route('tiket') }}" class="btn btn-outline-po fw-bold">Kembali</a>
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
