@extends('layouts.master')

@section('title', 'Edit Data Pelanggan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Edit Data Customer</h1>
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
            <form action="{{ route('update-data-pelanggan', $pelanggan->user_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama"
                        value="{{ $pelanggan->nama }}" required>
                </div>

                <div class="mb-3">
                    <input type="tel" class="form-control" name="no_hp" placeholder="Masukkan Nomor Telepon"
                        value="{{ $pelanggan->no_hp }}" required>
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control" name="alamat" placeholder="Masukkan Alamat"
                        value="{{ $pelanggan->alamat }}" required>
                </div>

                <div class="mb-3">
                    <input type="password" class="form-control" name="password"
                        placeholder="Masukkan Password Baru (opsional)">
                </div>

                <div class="divider"></div>

                <div class="button-container">
                    <a href="{{ route('data-pelanggan') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Update Akun</button>
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
