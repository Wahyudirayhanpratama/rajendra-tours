@extends('layouts.master6')

@section('title', 'Jadwal')

@section('content')

    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-light" role="status"></div>
    </div>
    <!-- * loader -->

    <!-- App Header -->
    <div class="loginbg bg-po">
        <div class="section">
            <div class="row mt-3 position-relative">
                <div class="line-dot"></div>
                <div class="col-5 mt-4">
                    <div class="fs-20 fw-600 text-white text-start mt-4"><span
                            class='city-schedule bg-po'>{{ strtoupper($cityfromSingkat) }}</span>
                    </div>
                </div>
                <div class="col-2 mt-5 text-center">
                    <div class="avatar-section">
                        <i class="uil uil-car fs-23 text-po w64 bg-white rounded-circle p-05"></i>
                    </div>
                </div>
                <div class="col-5 mt-4">
                    <div class="fs-20 fw-600 text-white text-end mt-4"><span
                            class='city-schedule bg-po'>{{ strtoupper($citytoSingkat) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- * App Header -->

    <!-- App Header -->
    <div class="appHeader bg-po">
        <div class="left">
            <a href="{{ route('cari-jadwal') }}" class="headerButton goBack">
                <i class="uil uil-angle-left-b fs-25 text-white"></i>
            </a>
        </div>
        <div class="pageTitle"></div>
        <div class="right">
        </div>
    </div>
    <!-- * App Header -->


    <div class="text-center"><input type="text" class="input-sm" id="changedate"></div>
    <div class="section" style="margin-top:-80px;">
        <div class="card">
            @php
                use Illuminate\Support\Carbon;
                $prevDate = Carbon::parse($tanggal)->subDay()->format('Y-m-d');
                $nextDate = Carbon::parse($tanggal)->addDay()->format('Y-m-d');
            @endphp
            <div class="card-header">
                <div class="row">
                    <div class="col-2 text-center">
                        <a href="{{ route('jadwal.cari', ['tanggal' => $prevDate]) }}">
                            <i class="uil uil-angle-left-b text-main fs-17"></i>
                        </a>
                    </div>
                    <div class="col-8 text-center">
                        <div class="fw-600 fs-16 text-main" id="changedateLabel">
                            <i class="uil uil-calendar-alt fs-20 fw-300"></i>{{ formatIndonesianDate($tanggal) }}
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <a href="{{ route('jadwal.cari', ['tanggal' => $nextDate]) }}">
                            <i class="uil uil-angle-right-b text-main fs-17"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-3 text-center fs-12 text-dark">
                        <div class="text-dark">
                            <i class="uil uil-car fs-15 text-dark"></i> Unit
                        </div>
                    </div>
                    <div class="col-3 text-center fs-12 text-dark">
                        <div class="text-dark">
                            <i class="uil uil-clock fs-15 text-dark"></i> Tujuan
                        </div>
                    </div>
                    <div class="col-3 text-center fs-12 text-dark">
                        <div class="text-dark">
                            <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png" width="18"
                                style="vertical-align: middle;"> Seat
                        </div>
                    </div>
                    <div class="col-3 text-center fs-12 text-dark">
                        <div class="text-dark">
                            <i class="uil uil-bill fs-15 text-dark"></i> Harga
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="appCapsule1" class="full-height p-y">

        <div class="section mt-1 mb-5">
            @forelse($jadwals as $jadwal)
                @php
                    $jadwalDateTime = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam_berangkat);
                    $isExpired = $jadwalDateTime->lt(now());
                @endphp
                <a href="{{ route('penumpang.create') }}?jadwal={{ $jadwal->jadwal_id }}">
                    <div class="card mb-1 {{ $isExpired ? 'bg-light opacity-50' : '' }}">
                        @if (!$isExpired)
                            <a href="{{ route('penumpang.create') }}?jadwal={{ $jadwal->jadwal_id }}"
                                class="stretched-link"></a>
                        @endif
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <div class="fs-13  text-dark">
                                        <div class=" text-dark mt-0 fw-600 fs-14">{{ $jadwal->mobil->nama_mobil }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="fs-13  text-dark">
                                        <div class=" text-dark mt-0 fs-14 fw-600 text-end">
                                            {{ $jadwal->mobil->nomor_polisi }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dot bg-blue dot_start"></div>
                            <div class="dot bg-blue dot_end"></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="fs-18 text-center  text-dark lh-15">{{ $jadwal->kota_tujuan }}</div>
                                    <div class="fs-18 text-center  text-dark" style="font-weight: bold">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_berangkat)->format('H:i') }} WIB</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-7">
                                    <div class="fs-13 text-start lh-15  text-dark fw-600 ">
                                        Sisa {{ $jadwal->kursi_tersisa ?? 0 }} Seat</div>
                                </div>
                                <div class="col-5">
                                    <div class="fs-12 text-end">
                                        <div class="mb-0 lh-17"><span class=" text-dark"><span class="fw-600 fs-15">Rp.
                                                    {{ number_format($jadwal->harga, 0, ',', '.') }}</span></div>
                                        @if ($isExpired)
                                            <div class="fs-12 text-danger lh-15">Lewat batas waktu pemesanan</div>
                                        @endif
                                        <div class="fs-12 text-end text-danger lh-15"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="alert alert-danger mt-3">
                    Tidak ada jadwal tersedia untuk kota dan tanggal tersebut.
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
            background-color: #92B6F8 !important;
        }

        .disabledcard {
            background: #EDEDF4;
        }
    </style>
@endpush

@push('scriptspwa')
@endpush
