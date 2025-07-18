@extends('layouts.master4')

@section('title', 'Bayar Tiket')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="appHeader bg-po">
            <div class="pageTitle text-white">BAYAR TIKET</div>
        </div>

        <!-- Info Detail Pemesanan: Desain Struk Modern -->
        <div class="mx-auto my-5" style="max-width: 600px;">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="text-center fw-bold mb-4 text-uppercase" style="letter-spacing: 1px;">Detail Pemesanan</h5>

                    <p class="text-center text-muted mb-4" style="font-size: 0.95rem;">Atas Nama:
                        <strong>{{ session('preview_pemesanan.nama') }}</strong>
                    </p>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Rute</span>
                        <span><strong>{{ session('preview_pemesanan.cityfrom') }} -
                                {{ session('preview_pemesanan.cityto') }}</strong></span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Tanggal</span>
                        <span><strong>{{ formatIndonesianDate(session('preview_pemesanan.tanggal')) }}</strong></span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Plat Nomor</span>
                        <span><strong>{{ session('preview_pemesanan.nomor_polisi') }}</strong></span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Jumlah Penumpang</span>
                        <span><strong>{{ session('preview_pemesanan.jumlah_penumpang') }} Orang</strong></span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Kode Booking</span>
                        <span><strong>{{ session('preview_pemesanan.kode_booking') }}</strong></span>
                    </div>

                    <div class="d-flex justify-content-between py-3 mt-2" style="font-size: 1.1rem;">
                        <span class="fw-bold text-dark">Total</span>
                        <span class="fw-bold text-success">Rp
                            {{ number_format(session('preview_pemesanan.total_harga'), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-grid gap-2 mt-5" style="max-width: 600px; margin: auto;">
            <button type="button" class="btn btn-po fw-bold" id="pay-button">
                <i class="bi bi-credit-card me-1"></i> Konfirmasi & Lanjutkan
            </button>
            {{-- Tombol Kembali hanya muncul jika status pembayaran lunas --}}
            @if ($pemesanan->status === 'lunas')
                <button class="btn btn-outline-po fw-bold" onclick="location.href='{{ route('tiket') }}'">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </button>
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

        .bayar-top {
            margin-top: 100px;
        }
    </style>
@endpush

@push('scriptspwa')
    <!-- Midtrans Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function() {
                const snapToken = @json($snapToken); // Pastikan snapToken tersedia
                if (!snapToken) {
                    alert("Snap token tidak tersedia.");
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log("Pembayaran sukses", result);
                        // redirect ke halaman sukses / simpan status?
                    },
                    onPending: function(result) {
                        console.log("Menunggu pembayaran", result);
                    },
                    onError: function(result) {
                        console.log("Pembayaran gagal", result);
                    },
                    onClose: function() {
                        alert("Kamu menutup popup tanpa menyelesaikan pembayaran");
                    }
                });
            });
        });
    </script>
@endpush
