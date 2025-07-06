@extends('layouts.master')

@section('title', 'Edit Data Penumpang')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Edit Penumpang</h1>
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

            <form action="{{ route('update-data-penumpang', $penumpang->penumpang_id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nama Otomatis dari User -->
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Pemesan</label>
                    <input type="text" class="form-control" value="{{ $penumpang->pemesanan->user->nama ?? '-' }}"
                        readonly>
                </div>

                <!-- No HP -->
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No HP</label>
                    <input type="text" class="form-control" value="{{ $penumpang->pemesanan->user->no_hp ?? '-' }}"
                        readonly>
                </div>

                <!-- Jenis Kelamin -->
                <div class="mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="L" {{ $penumpang->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $penumpang->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Nomor Kursi -->
                <input type="hidden" name="jumlah_penumpang" class="form-control"
                    value="{{ $pemesanan->jumlah_penumpang }}">
                <div class="mb-3">
                    <label for="nomor_kursi">Nomor Kursi</label>
                    <input type="hidden" name="nomor_kursi" id="selectedSeatsInput" value="{{ $penumpang->nomor_kursi }}">
                    <select class="form-select" id="nomor_kursi">
                        <option selected disabled>Pilih Kursi</option>
                    </select>
                    <div class="mt-2" id="selectedSeatsDisplay"></div>
                </div>

                <!-- Alamat Jemput -->
                <div class="mb-3">
                    <label for="alamat_jemput" class="form-label">Alamat Jemput</label>
                    <input type="text" name="alamat_jemput" class="form-control" value="{{ $penumpang->alamat_jemput }}"
                        required>
                </div>

                <!-- Alamat Antar -->
                <div class="mb-3">
                    <label for="alamat_antar" class="form-label">Alamat Antar</label>
                    <input type="text" name="alamat_antar" class="form-control" value="{{ $penumpang->alamat_antar }}"
                        required>
                </div>

                <!-- Info Jadwal -->
                <div class="mb-3">
                    <label class="form-label">Tanggal & Jam Keberangkatan</label>
                    <input type="text" class="form-control"
                        value="{{ formatIndonesianDate($penumpang->pemesanan->jadwal->tanggal) }} - {{ $penumpang->pemesanan->jadwal->jam_berangkat }}"
                        readonly>
                </div>

                <!-- Tombol -->
                <div class="button-container">
                    <a href="{{ route('data.penumpang') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Update Penumpang</button>
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
            margin-bottom: 1cm;
        }

        .form-control,
        .form-select {
            border: 1px solid #ddd;
            height: 45px;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 10px;
            padding: 5px;
        }

        .form-row .form-select {
            flex: 1;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const kursiSelect = document.getElementById('nomor_kursi');
        const jumlahPenumpangInput = document.querySelector('input[name="jumlah_penumpang"]');
        const selectedSeatsInput = document.getElementById('selectedSeatsInput');
        const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');

        let kapasitas = parseInt(@json($jumlah_kursi_mobil));
        let selectedSeats = [];
        let kursiTerpakai = @json($kursiTerpakai);
        const currentSeats = @json($currentSeats);

        function getCurrentKursiTerpakai() {
            const currentJadwalId = @json($pemesanan->jadwal_id);
            return kursiTerpakai[currentJadwalId] || [];
        }

        function generateKursiOptions() {
            const jumlah = parseInt(jumlahPenumpangInput?.value || currentSeats.length || 1);
            kursiSelect.innerHTML = '<option selected disabled>Pilih Kursi</option>';

            const currentTerpakai = getCurrentKursiTerpakai();

            const sisaKursi = kapasitas - currentTerpakai.length;

            if (sisaKursi <= 0 && selectedSeats.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Tidak ada kursi tersedia';
                opt.disabled = true;
                kursiSelect.appendChild(opt);
                return;
            }

            for (let i = 1; i <= kapasitas; i++) {
                const iStr = i.toString();
                if (!currentTerpakai.includes(iStr) || selectedSeats.includes(iStr)) {
                    const opt = document.createElement('option');
                    opt.value = iStr;
                    opt.textContent = `Kursi ${i}`;
                    kursiSelect.appendChild(opt);
                }
            }

            updateSelectedSeats();
        }

        kursiSelect.addEventListener('change', () => {
            const selected = kursiSelect.value;
            if (!selectedSeats.includes(selected)) {
                if (selectedSeats.length < parseInt(jumlahPenumpangInput?.value || currentSeats.length)) {
                    selectedSeats.push(selected);
                    updateSelectedSeats();
                    generateKursiOptions();
                } else {
                    alert("Jumlah kursi yang dipilih melebihi jumlah penumpang.");
                }
            }
            kursiSelect.selectedIndex = 0;
        });

        function updateSelectedSeats() {
            selectedSeatsInput.value = selectedSeats.join(',');
            selectedSeatsDisplay.innerHTML = '';
            selectedSeats.forEach((seat, index) => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary me-1';
                badge.textContent = `Kursi ${seat}`;
                badge.style.cursor = 'pointer';
                badge.onclick = () => {
                    selectedSeats.splice(index, 1);
                    generateKursiOptions();
                };
                selectedSeatsDisplay.appendChild(badge);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            selectedSeats = currentSeats.map(String);
            generateKursiOptions();
        });
        console.log("currentSeats:", currentSeats);
        console.log("kursiTerpakai:", kursiTerpakai);
        console.log("Terpakai untuk jadwal ini:", getCurrentKursiTerpakai());
    </script>
@endpush
