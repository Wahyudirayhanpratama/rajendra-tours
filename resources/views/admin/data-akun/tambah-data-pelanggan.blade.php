@extends('layouts.master')

@section('title', 'Tambah Data Pelanggan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Tambah Data Customer</h1>
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
            <form action="{{ route('store-pelanggan') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required>
                        </div>

                        <div class="mb-3">
                            <input type="tel" name="no_hp" class="form-control" placeholder="Masukkan Nomor Telepon"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <input type="text" name="alamat" class="form-control" placeholder="Masukkan Alamat"
                                required>
                        </div>

                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Masukkan Password"
                                required>
                        </div>
                    </div>
                </div>

                <div class="button-container">
                    <a href="{{ route('data-pelanggan') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Tambah</button>
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
