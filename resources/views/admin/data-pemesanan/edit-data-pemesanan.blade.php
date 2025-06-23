@extends('layouts.master')

@section('title', 'Edit Data Pemesanan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Edit Data Pemesanan</h1>
                </div>
            </div>
        </section>

        <div class="container">
            <form action="{{ route('update-data-pemesanan', $pemesanan->pemesanan_id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="user_id" value="{{ $pemesanan->user_id }}">
                <input type="hidden" name="penumpang_id" value="{{ $penumpang->penumpang_id }}">

                <!-- Nama -->
                <div class="mb-3">
                    <label for="nama-pemesan">Nama Pemesan</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $penumpang->nama) }}"
                        required>
                </div>

                <!-- Nomor Telepon -->
                <div class="mb-3">
                    <label for="nomor-telepon">Nomor Telepon</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $penumpang->no_hp) }}"
                        required>
                </div>

                <!-- Jenis Kelamin -->
                <div class="mb-3">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="L" {{ $penumpang->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $penumpang->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tujuan -->
                <div class="mb-3">
                    <label for="kota_tujuan">Tujuan</label>
                    <select name="kota_tujuan" class="form-select" required>
                        <option value="Duri" {{ $pemesanan->jadwal->kota_tujuan == 'Duri' ? 'selected' : '' }}>Duri
                        </option>
                        <option value="Pekanbaru" {{ $pemesanan->jadwal->kota_tujuan == 'Pekanbaru' ? 'selected' : '' }}>
                            Pekanbaru
                        </option>
                        <option value="Padang" {{ $pemesanan->jadwal->kota_tujuan == 'Padang' ? 'selected' : '' }}>Padang
                        </option>
                    </select>
                </div>


                <!-- Alamat Jemput -->
                <div class="mb-3">
                    <label for="alamat-jemput">Alamat Jemput</label>
                    <input type="text" name="alamat_jemput" class="form-control"
                        value="{{ old('alamat_jemput', $penumpang->alamat_jemput) }}" required>
                </div>

                <!-- Alamat Antar -->
                <div class="mb-3">
                    <label for="alamat-antar">Alamat Antar</label>
                    <input type="text" name="alamat_antar" class="form-control"
                        value="{{ old('alamat_antar', $penumpang->alamat_antar) }}" required>
                </div>

                <!-- Tanggal Berangkat -->
                <div class="mb-3">
                    <label for="tanggal">Tanggal Berangkat</label>
                    <select id="tanggal" class="form-select" onchange="filterJadwalByTanggal(this.value)" required>
                        <option value="">-- Pilih Tanggal --</option>
                        @foreach ($tanggalList as $tanggal)
                            <option value="{{ $tanggal }}"
                                {{ $pemesanan->jadwal->tanggal == $tanggal ? 'selected' : '' }}>
                                {{ formatIndonesianDate($tanggal) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah Penumpang -->
                <div class="mb-3">
                    <label for="jumlah_penumpang">Jumlah Penumpang</label>
                    <input type="number" name="jumlah_penumpang" class="form-control"
                        value="{{ old('jumlah_penumpang', $pemesanan->jumlah_penumpang) }}" min="1" max="5"
                        required>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status">Status Pembayaran</label>
                    <select name="status" class="form-select" required>
                        <option value="belum lunas" {{ $pemesanan->status == 'belum lunas' ? 'selected' : '' }}>Belum Lunas
                        </option>
                        <option value="lunas" {{ $pemesanan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <!-- Travel -->
                <div class="mb-3">
                    <label for="jadwal_id">Travel</label>
                    <select name="jadwal_id" class="form-select" id="jadwal-select" required>
                        <option disabled selected>-- Pilih Travel --</option>
                        @foreach ($jadwals as $jadwal)
                            <option value="{{ $jadwal->jadwal_id }}" data-tanggal="{{ $jadwal->tanggal }}"
                                data-kapasitas="{{ $jadwal->mobil->kapasitas }}"
                                {{ $pemesanan->jadwal_id == $jadwal->jadwal_id ? 'selected' : '' }}>
                                {{ $jadwal->mobil->nomor_polisi }} - {{ $jadwal->kota_asal }} ke
                                {{ $jadwal->kota_tujuan }}
                                ({{ formatIndonesianDate($jadwal->tanggal) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor Kursi -->
                <div class="mb-3">
                    <label for="nomor_kursi">Nomor Kursi</label>
                    <input type="hidden" name="nomor_kursi" id="selectedSeatsInput" value="{{ $penumpang->nomor_kursi }}">
                    <select class="form-select" id="nomor_kursi">
                        <option selected disabled>Pilih Kursi</option>
                    </select>
                    <div class="mt-2" id="selectedSeatsDisplay"></div>
                </div>

                <!-- Tombol Aksi -->
                <div class="button-container">
                    <a href="{{ route('data-pemesanan') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Update</button>
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
        function filterJadwalByTanggal(selectedTanggal) {
            const jadwalSelect = document.getElementById('jadwal-select');
            const options = jadwalSelect.options;
            let matchingOptions = [];

            const previousSelected = jadwalSelect.value; // simpan value sebelumnya
            let isPreviousStillValid = false;

            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                const tanggal = opt.getAttribute('data-tanggal');

                if (!selectedTanggal || tanggal === selectedTanggal) {
                    opt.style.display = 'block';
                    matchingOptions.push(opt);

                    // cek apakah opsi sebelumnya masih valid
                    if (opt.value === previousSelected) {
                        isPreviousStillValid = true;
                    }

                } else {
                    opt.style.display = 'none';
                }
            }

            // jika opsi sebelumnya tidak valid, kosongkan
            if (!isPreviousStillValid) {
                jadwalSelect.value = '';
            }

            // jika hanya ada 1 opsi travel yang cocok, pilih otomatis
            if (matchingOptions.length === 1) {
                jadwalSelect.value = matchingOptions[0].value;
            }
        }
    </script>
    <script>
        const jadwalSelect = document.getElementById('jadwal-select');
        const kursiSelect = document.getElementById('nomor_kursi');
        const jumlahPenumpangInput = document.querySelector('input[name="jumlah_penumpang"]');

        const selectedSeatsInput = document.getElementById('selectedSeatsInput');
        const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');

        let kapasitas = parseInt(@json($jumlah_kursi_mobil));
        let selectedSeats = [];
        let kursiTerpakaiSemuaJadwal = @json($kursiTerpakai);
        const currentJadwalId = @json($pemesanan->jadwal_id);
        const currentSeats = @json(explode(',', $penumpang->nomor_kursi ?? ''));

        // Muat kursi terpakai berdasarkan jadwal yang dipilih
        function loadKursiTerpakai(jadwalId) {
            let kursiTerpakai = kursiTerpakaiSemuaJadwal[jadwalId] || [];
            return kursiTerpakai.map(k => k.trim()).filter(k => !currentSeats.includes(k));
        }

        function generateKursiOptions() {
            const jumlah = parseInt(jumlahPenumpangInput.value || 1);
            kursiSelect.innerHTML = '<option selected disabled>Pilih Kursi</option>';

            const sisaKursi = kapasitas - kursiTerpakai.length;

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
                if (!kursiTerpakai.includes(iStr) || selectedSeats.includes(iStr)) {
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
                if (selectedSeats.length < parseInt(jumlahPenumpangInput.value)) {
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

        jadwalSelect.addEventListener('change', () => {
            const selectedOption = jadwalSelect.selectedOptions[0];
            const jadwalId = selectedOption.value;
            kapasitas = parseInt(selectedOption.dataset.kapasitas);
            kursiTerpakai = loadKursiTerpakai(jadwalId);
            selectedSeats = []; // reset ketika jadwal berubah
            updateSelectedSeats();
            generateKursiOptions();
        });

        jumlahPenumpangInput.addEventListener('input', () => {
            generateKursiOptions();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const selectedOption = jadwalSelect.querySelector(`option[value="${currentJadwalId}"]`);
            kapasitas = selectedOption ? parseInt(selectedOption.dataset.kapasitas) : kapasitas;
            kursiTerpakai = loadKursiTerpakai(currentJadwalId);
            selectedSeats = [...currentSeats];
            generateKursiOptions();
        });
    </script>
@endpush
