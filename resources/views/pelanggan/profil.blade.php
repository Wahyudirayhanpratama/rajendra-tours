@extends('layouts.master4')

@section('title', 'Profil')

@section('content')

    <div class="container d-flex justify-content-center">
        <div style="max-width: 500px; width: 100%;">
            <!-- Card Profil -->
            <div class="card shadow rounded-4 p-3 mb-4 border-0">
                <div class="d-flex align-items-center">
                    @auth('pelanggan')
                        @php
                            $nama = Auth::guard('pelanggan')->user()->nama;
                            $inisial = '';
                            $parts = explode(' ', trim($nama));

                            if (count($parts) >= 2) {
                                $inisial = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                            } else {
                                $inisial = strtoupper(substr($parts[0], 0, 2));
                            }
                        @endphp
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3"
                            style="width: 70px; height: 70px; font-size: 24px;">
                            {{ $inisial }}</div>
                        <div>
                            <p class="mb-1 fw-bold">{{ Auth::guard('pelanggan')->user()->nama ?? 'Nama tidak tersedia' }}</p>
                            <p class="mb-1" style="font-size: 0.9rem;">
                                {{ Auth::guard('pelanggan')->user()->no_hp ?? 'Nomor HP tidak tersedia' }}
                            </p>
                            <p class="mb-0" style="font-size: 0.9rem;">
                                {{ Auth::guard('pelanggan')->user()->alamat ?? 'Alamat belum diisi' }}</p>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Menu Items -->
            <div class="d-grid gap-3">
                <a href="#"
                    class="d-flex justify-content-between align-items-center px-3 py-3 shadow bg-white rounded-4 text-decoration-none text-dark border-0"
                    data-bs-toggle="modal" data-bs-target="#profilModal" style="cursor:pointer;">
                    <div><i class="bi bi-person me-2"></i> Profil Saya</div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('cari-jadwal') }}"
                    class="d-flex justify-content-between align-items-center px-3 py-3 shadow bg-white rounded-4 text-decoration-none text-dark border-0">
                    <div><i class="bi bi-calendar-event me-2"></i> Cari Jadwal</div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('riwayat') }}"
                    class="d-flex justify-content-between align-items-center px-3 py-3 shadow bg-white rounded-4 text-decoration-none text-dark border-0">
                    <div><i class="bi bi-clipboard me-2"></i> Riwayat Pesanan</div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('tiket') }}"
                    class="d-flex justify-content-between align-items-center px-3 py-3 shadow bg-white rounded-4 text-decoration-none text-dark border-0">
                    <div><i class="bi bi-ticket-perforated me-2"></i> Tiket Saya</div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="#" data-confirm="logout"
                    class="d-flex justify-content-between align-items-center px-3 py-3 shadow bg-white rounded-4 text-decoration-none text-dark border-0">
                    <div><i class="bi bi-box-arrow-right me-2"></i> Keluar</div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <form id="logout-form" action="{{ route('logout.pelanggan') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Profil -->
    <div class="modal modal-bottom" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-fullscreen-sm-down">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">PROFIL SAYA</h6>
                        <a href="#" data-bs-dismiss="modal">
                            <i class="uil uil-multiply fs-18 text-dark"></i>
                        </a>
                    </div>
                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        @auth('pelanggan')
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-person icon-regist"></i></span>
                                <input type="text" name="nama" class="form-control" placeholder="Nama"
                                    value="{{ Auth::guard('pelanggan')->user()->nama ?? '' }}">
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="bi bi-telephone icon-regist"></i></span>
                                <input type="text" name="no_hp" class="form-control" placeholder="Telepon"
                                    value="{{ Auth::guard('pelanggan')->user()->no_hp ?? '' }}">
                            </div>
                            <div class="mb-4 input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt icon-regist"></i></span>
                                <input type="text" name="alamat" class="form-control" placeholder="Alamat"
                                    value="{{ Auth::guard('pelanggan')->user()->alamat ?? '' }}">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-po">SIMPAN</button>
                            </div>
                        @endauth
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('headerspwa')
    <style>
        .icon-regist {
            margin-left: 10px;
        }

        .no-scroll {
            overflow: hidden;
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
