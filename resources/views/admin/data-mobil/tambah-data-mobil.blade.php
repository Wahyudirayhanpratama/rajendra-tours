@extends('layouts.master')

@section('title', 'Tambah Data Mobil')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Tambah Data Mobil</h1>
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
            <form action="{{ route('store-data-mobil') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" name="nama_mobil" placeholder="Masukkan Merk Mobil" required>
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control" name="nomor_polisi" placeholder="Masukkan Nomor Polisi"
                        required>
                </div>

                <div class="mb-3">
                    <input type="number" class="form-control" name="kapasitas" placeholder="Jumlah Seat" required>
                </div>

                <div class="mb-3">
                    <input type="file" class="form-control" name="gambar" placeholder="Upload Gambar" accept="image/*" required>
                </div>

                <div class="divider"></div>

                <div class="button-container d-flex justify-content-between">
                    <a href="{{ route('data-mobil') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary bg-po">Tambah</button>
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
