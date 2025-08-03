@extends('layouts.master7')

@section('title', 'Tiket Saya')

@section('content')
    <!-- App Header -->
    <div class="appHeader bg-po">
        <div class="left">
            <a href="{{ route('cari-jadwal') }}" class="headerButton goBack">
                <i class="uil uil-angle-left-b fs-25 text-white"></i>
            </a>
        </div>
        <div class="pageTitle text-white">TIKET SAYA</div>
    </div>
    <!-- * App Header -->
    <style>

    </style>

    <div id="appCapsule2" class="full-height p-y">

        <div class="section mt-5 mb-5">
            {{-- Tiket Aktif --}}
            @forelse($pemesanansAktif as $pemesanan)
                <a href="{{ route('detail.tiket', $pemesanan->pemesanan_id) }}">
                    <div class="card mb-1">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <div class="p-2 border rounded d-flex justify-content-between align-items-center"
                                        style="height: 36px; width: 7cm;">
                                        <span class="fw-bold fs-7 mb-0">No Tiket:
                                            <strong>{{ $pemesanan->tiket->no_tiket ?? '-' }}</strong></span>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    @php
                                        $status = $pemesanan->status ?? 'unknown';

                                        switch ($status) {
                                            case 'lunas':
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
                                        $labelStatus = match ($status) {
                                            'lunas' => 'Lunas',
                                            'belum_lunas' => 'Menunggu Pembayaran',
                                            'Tiket dibatalkan' => 'Tiket Dibatalkan',
                                            default => ucfirst(str_replace('_', ' ', $status)),
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $labelStatus }}</span>
                                </div>
                            </div>
                            <div class="dot bg-blue dot_start"></div>
                            <div class="dot bg-blue dot_end"></div>
                        </div>
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-12 col-sm-10">
                                    <div class="d-flex justify-content-between align-items-center fs-20 fw-bold text-dark">
                                        <span class="fw-bold fs-6 fs-sm-5 text-start">
                                            {{ singkatanKota($pemesanan->jadwal->kota_asal ?? '?') }}</span>

                                        <span class="flex-grow-1 text-center d-none d-sm-block">
                                            <span style="font-size: 18px;">⭘</span>
                                            <span class="d-inline-block"
                                                style="letter-spacing: 3px;">----------------------</span>
                                            <span style="font-size: 18px;">→</span>
                                        </span>
                                        <!-- Gaya alternatif untuk mobile -->
                                        <span class="w-100 text-center d-block d-sm-none my-2">
                                            <span class="d-inline-block mx-2" style="font-size: 12px;">→</span>
                                        </span>

                                        <span class="fw-bold fs-6 fs-sm-5 text-end">
                                            {{ singkatanKota($pemesanan->jadwal->kota_tujuan ?? '?') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-7">
                                    <div class="fs-13 text-start lh-15 text-dark fw-600">
                                        {{ formatIndonesianDate($pemesanan->jadwal->tanggal) }}
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="fs-13 text-end lh-15 text-dark fw-600">
                                        Rp. {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-muted alert alert-secondary mt-3 mx-2">
                    Belum ada tiket aktif.
                </div>
            @endforelse
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

        .disabledcard {
            background: #EDEDF4;
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
