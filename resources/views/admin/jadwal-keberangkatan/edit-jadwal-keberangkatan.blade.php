@extends('layouts.master')

@section('title', 'Edit Jadwal Keberangkatan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Edit Jadwal Keberangkatan</h1>
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
            <form action="{{ route('update-jadwal-keberangkatan', $jadwal->jadwal_id) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Nomor Polisi -->
                <div class="mb-3">
                    <label for="mobil_id">Pilih Mobil</label>
                    <select name="mobil_id" class="form-select" required>
                        @foreach ($mobils as $mobil)
                            <option value="{{ $mobil->mobil_id }}"
                                {{ $jadwal->mobil_id == $mobil->mobil_id ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} - {{ $mobil->nama_mobil }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kota Asal -->
                @php
                    $semuaKota = ['Duri', 'Pekanbaru', 'Padang'];
                @endphp
                <div class="mb-3">
                    <label for="kota_asal">Kota Asal</label>
                    <select name="kota_asal" id="kota_asal" class="form-select" required onchange="filterTujuanEdit()">
                        <option value="" disabled {{ $jadwal->kota_asal ? '' : 'selected' }}>Pilih Kota Asal</option>
                        @foreach ($semuaKota as $kota)
                            <option value="{{ $kota }}" {{ $jadwal->kota_asal === $kota ? 'selected' : '' }}>
                                {{ $kota }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kota Tujuan -->
                <div class="mb-3">
                    <label for="kota_tujuan">Kota Tujuan</label>
                    <select name="kota_tujuan" id="kota_tujuan" class="form-select" required>
                        <option value="" disabled {{ $jadwal->kota_tujuan ? '' : 'selected' }}>Pilih Kota Tujuan
                        </option>
                        @foreach ($semuaKota as $kota)
                            @if ($kota !== $jadwal->kota_asal)
                                {{-- Hindari kota yang sama --}}
                                <option value="{{ $kota }}"
                                    {{ $jadwal->kota_tujuan === $kota ? 'selected' : '' }}>{{ $kota }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Jam Keberangkatan -->
                <div class="mb-3">
                    <label for="jam_berangkat">Jam Keberangkatan</label>
                    <input type="time" name="jam_berangkat" class="form-control" value="{{ $jadwal->jam_berangkat }}"
                        required>
                </div>

                <!-- Tanggal Keberangkatan -->
                <div class="mb-3">
                    <label for="tanggal">Tanggal Keberangkatan</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $jadwal->tanggal }}" required>
                </div>

                <!-- Harga -->
                <div class="mb-3">
                    <label for="harga">Harga Tiket (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="{{ $jadwal->harga }}" required>
                </div>

                <!-- Tombol Aksi -->
                <div class="button-container">
                    <a href="{{ route('jadwal-keberangkatan') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Update Jadwal</button>
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
        }
    </style>
@endpush

@push('scripts')
    <script>
        const semuaKota = @json($semuaKota);
        const kotaTujuanTerpilih = "{{ $jadwal->kota_tujuan }}";

        function filterTujuanEdit() {
            const asal = document.getElementById("kota_asal").value;
            const tujuanSelect = document.getElementById("kota_tujuan");

            // Reset opsi
            tujuanSelect.innerHTML = '<option value="" disabled selected>Pilih Kota Tujuan</option>';

            // Tampilkan kota selain kota asal
            semuaKota.forEach(kota => {
                if (kota !== asal) {
                    const option = document.createElement("option");
                    option.value = kota;
                    option.text = kota;
                    if (kota === kotaTujuanTerpilih) {
                        option.selected = true;
                    }
                    tujuanSelect.appendChild(option);
                }
            });
        }

        // Jalankan sekali untuk inisialisasi saat load
        document.addEventListener("DOMContentLoaded", function() {
            filterTujuanEdit();
        });
    </script>
@endpush
