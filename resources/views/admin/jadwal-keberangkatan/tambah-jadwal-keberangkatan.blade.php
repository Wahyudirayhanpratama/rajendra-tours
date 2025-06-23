@extends('layouts.master')

@section('title', 'Tambah Jadwal Keberangkatan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Tambah Jadwal Keberangkatan</h1>
                </div>
            </div>
        </section>

        <div class="container">
            <form action="{{ route('store-jadwal-keberangkatan') }}" method="POST">
                @csrf
                <!-- Nomor Polisi -->
                <div class="mb-3">
                    <label for="mobil_id">Masukkan Nomor Polisi</label>
                    <select name="mobil_id" class="form-select" required>
                        <option selected disabled>Pilih Nomor Polisi</option>
                        @foreach ($mobils as $mobil)
                            <option value="{{ $mobil->mobil_id }}">{{ $mobil->nomor_polisi }} - {{ $mobil->nama_mobil }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rute -->
                <div class="mb-3">
                    <label for="kota_asal">Kota Asal</label>
                    <input type="text" name="kota_asal" class="form-control" placeholder="Contoh: Duri" required>
                </div>

                <div class="mb-3">
                    <label for="kota_tujuan">Kota Tujuan</label>
                    <input type="text" name="kota_tujuan" class="form-control" placeholder="Contoh: Pekanbaru" required>
                </div>

                <!-- Jam Keberangkatan -->
                <div class="mb-3">
                    <label for="jam_berangkat">Jam Keberangkatan</label>
                    <input type="time" name="jam_berangkat" class="form-control" required>
                </div>

                <!-- Tanggal Berangkat -->
                <div class="mb-3">
                    <label for="tanggal">Tanggal Keberangkatan</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <!-- Harga -->
                <div class="mb-3">
                    <label for="harga">Harga Tiket (Rp)</label>
                    <input type="number" name="harga" class="form-control" placeholder="Contoh: 100000" required>
                </div>

                <!-- Tombol Aksi -->
                <div class="button-container">
                    <a href="{{ route('jadwal-keberangkatan') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Tambah Jadwal</button>
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

        .form-control,
        .form-select {
            border: 1px solid #ddd;
            height: 45px;
            margin-bottom: 20px;
        }
    </style>
@endpush
