@extends('layouts.master')

@section('title', 'Data Mobil')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Data Mobil</h1>
                    <a href="{{ route('tambah-data-mobil') }}" class="btn btn-tambah text-white">Tambah Mobil</a>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Card 1 -->
                    @forelse ($mobils as $mobil)
                        <div class="col-md-4 mb-4">
                            <div class="card-mobil elevation-2">
                                <div class="card-body">
                                    <!-- Header: Nama dan Plat Mobil dalam satu baris -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold text-light fs-4">{{ $mobil->nama_mobil }}</span>
                                    </div>
                                    <p class="mb-1 mt-1">{{ $mobil->nomor_polisi }}</p>
                                    <!-- Detail lainnya -->
                                    @php
                                        $jadwal = $mobil->jadwals->first(); // misalnya hanya tampilkan 1 jadwal pertama
                                    @endphp
                                    @if ($jadwal)
                                        <p class="mb-1 mt-2">Jam Berangkat
                                            {{ \Carbon\Carbon::parse($jadwal->jam_berangkat)->format('H:i') }} WIB</p>
                                    @else
                                        <p class="mb-1 mt-2 text-warning">Belum ada jadwal</p>
                                    @endif
                                    <p class="mb-3">
                                        {{ $mobil->kapasitas }} Seat -
                                        <span class="text-secondary">
                                            Tersisa {{ $mobil->kursi_tersisa }} kursi
                                        </span>
                                    </p>

                                    <!-- Tombol Aksi -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('edit-data-mobil', $mobil->mobil_id) }}"
                                            class="edit-btn btn-sm mr-2">Edit</a>
                                        <form action="{{ route('hapus-data-mobil', $mobil->mobil_id) }}" method="POST"
                                            data-confirm="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning text-center">
                                Data mobil kosong.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
