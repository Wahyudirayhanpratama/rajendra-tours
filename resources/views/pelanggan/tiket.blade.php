@extends('layouts.master7')

@section('title', 'Tiket Saya')

@section('content')

    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-light" role="status"></div>
    </div>
    <!-- * loader -->

    <!-- App Header -->
    <div class="appHeader bg-po">
        <div class="left">
            <a href="#" class="headerButton goBack">
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
                                        style="height: 36px; width: 6cm;">
                                        <span class="fw-bold fs-7 mb-0">No Tiket:
                                            <strong>{{ $pemesanan->tiket->no_tiket ?? '-' }}</strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="dot bg-blue dot_start"></div>
                            <div class="dot bg-blue dot_end"></div>
                        </div>
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-10">
                                    <div class="d-flex justify-content-between align-items-center fs-20 fw-bold text-dark">
                                        <span>{{ $cityfromSingkat }}</span>
                                        <span style="flex-grow: 1; text-align: center;">
                                            <span style="font-size: 18px;">⭘</span>
                                            <span style="letter-spacing: 3px;">----------------------</span>
                                            <span style="font-size: 18px;">→</span>
                                        </span>
                                        <span>{{ $citytoSingkat }}</span>
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
                                <div class="col-5 text-end">
                                    <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo"
                                        style="height: 20px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-muted alert alert-secondary mt-3">
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
@endpush
