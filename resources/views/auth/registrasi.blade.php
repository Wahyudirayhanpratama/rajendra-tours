@extends('layouts.master4')

@section('title', 'Registrasi')

@section('content')
    <!-- Form Registrasi -->
    <div class="container-fluid p-0 m-0">
        <!-- Bagian Background (warna biru) -->
        <div class="bg-po pt-5" style="min-height: 80vh;">
            <!-- Judul -->
            <h5 class="text-center text-white pt-5">Daftar Member Rajendra Tours</h5>
            <!-- Card Registrasi -->
            <div class="card border-0 rounded-top-4" style="min-height: 80vh;">
                <div class="p-4 p-md-5">
                    <form action="{{ route('register.pelanggan.submit') }}" method="POST">
                        @csrf
                        <!-- Nama -->
                        <div class="form-group mb-2">
                            <div class="text-dark fw-semibold">Nama</div>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user text-dark icon-regist"></i>
                                </span>
                                <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama"
                                    required>
                            </div>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="form-group mb-2">
                            <div class="text-dark fw-semibold">Nomor Telepon</div>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-phone text-dark icon-regist"></i>
                                </span>
                                <input type="tel" name="no_hp" class="form-control border-start-0"
                                    placeholder="Masukkan Nomor Telepon" required>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="form-group mb-2">
                            <div class="text-dark fw-semibold">Alamat</div>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-map-marker-alt text-dark icon-regist"></i>
                                </span>
                                <input type="text" name="alamat" class="form-control border-start-0"
                                    placeholder="Masukkan Alamat" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group mb-2">
                            <div class="text-dark fw-semibold">Password</div>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-lock text-dark icon-regist"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-start-0"
                                    placeholder="Masukkan Password" required>
                            </div>
                        </div>

                        <!-- Tombol Registrasi -->
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn bg-po text-white fw-bold">Registrasi</button>
                        </div>
                    </form>

                    <!-- Link Login -->
                    <div class="text-left mb-2" style="font-size: small">
                        <small>Sudah punya akun? <a href="/login-pelanggan">Login</a></small>
                    </div>

                    <!-- Garis atau -->
                    <div class="text-center my-2 text-muted">ATAU</div>

                    <!-- Tombol Cari Jadwal -->
                    <div class="d-grid mt-4">
                        <a href="{{ route('cari-jadwal') }}" class="btn btn-outline-primary fw-bold btn-outline-po">Cari Jadwal</a>
                    </div>
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
            height: 13vh;
            padding-top: 20px;
            position: relative;
        }

        .left {
            position: absolute;
            top: 25px;
            /* Atur jarak dari atas */
            left: 16px;
            /* Atur jarak dari kiri */
            /* Styling lain untuk tombol back */
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

        .btn-outline-po {
            border-color: #000080;
            color: #000080;
        }

        .btn-outline-po:hover {
            background-color: #000080 !important;
            color: white !important;
        }

        .input-group-text {
            padding-left: 0.5rem;
            /* Tambahkan ruang di kiri ikon */
            padding-right: 0.5rem;
            /* Tambahkan ruang di kanan ikon */
            border-right: none !important;
        }

        .card.rounded-top-4 {
            border-top-left-radius: 50px !important;
            border-top-right-radius: 50px !important;
            border-bottom-left-radius: 0%;
            border-bottom-right-radius: 0%;
            margin-top: 40px;
        }

        /* New Styles for the Form */
        .input-field {
            border-left: none !important;
        }

        .input-group {
            border-radius: 0.375rem;
            overflow: hidden;
            /* Tambahkan ini untuk mencegah border overlap */
        }

        .input-group-text {
            background-color: white;
            border-right: none !important;
            padding: 0.375rem 0.75rem;
        }

        .input-group:focus-within {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-control {
            border-left: none !important;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: #ced4da;
        }

        /* Memastikan border tetap terlihat saat focus */
        .input-group:focus-within {
            border: 1px solid #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Menghilangkan border radius yang tidak perlu */
        .input-group-text:first-child {
            border-top-left-radius: 0.375rem !important;
            border-bottom-left-radius: 0.375rem !important;
        }

        .form-control:last-child {
            border-top-right-radius: 0.375rem !important;
            border-bottom-right-radius: 0.375rem !important;
        }

        .icon-regist {
            margin-left: 10px;
        }
    </style>
@endpush
