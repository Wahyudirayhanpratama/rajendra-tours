@extends('layouts.master')

@section('title', 'Edit Data Mobil')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Edit Data Mobil</h1>
                </div>
            </div>
        </section>

        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('update-data-mobil', $mobil->mobil_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama-mobil">Masukkan Merk Mobil</label>
                    <input type="text" class="form-control" name="nama_mobil" placeholder="Contoh: Toyota Avanza"
                        value="{{ $mobil->nama_mobil }}" required>
                </div>

                <div class="mb-3">
                    <label for="nomor-polisi">Masukkan Nomor Polisi</label>
                    <input type="text" class="form-control" name="nomor_polisi" placeholder="Contoh: B 1234 ABC"
                        value="{{ $mobil->nomor_polisi }}" required>
                </div>

                <div class="mb-3">
                    <label for="jumlah-seat">Jumlah Seat</label>
                    <input type="number" class="form-control" name="kapasitas" placeholder="Contoh: 7"
                        value="{{ $mobil->kapasitas }}" required>
                </div>

                <div class="mb-3">
                    <label for="gambar">Upload Gambar Mobil</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*">
                </div>

                <div class="divider"></div>

                <div class="button-container d-flex justify-content-between">
                    <a href="{{ route('data-mobil') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary bg-po">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('headers')
    <style>
        .container {
            max-width: 98%;
            overflow-x: auto;
            margin-left: 10px;
        }
    </style>
@endpush
