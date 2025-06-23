@extends('layouts.master')

@section('title', 'Tambah Data Pemesanan')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Tambah Data Pemesanan</h1>
                </div>
            </div>
        </section>

        <div class="container">
            <form action="{{ route('store-data-pemesanan') }}" method="POST">
                @csrf
                <!-- Nama -->
                <label for="user_id">Nama Pemesan</label>
                <select name="user_id" class="form-select" id="user_id" required>
                    <option selected disabled>Pilih Pelanggan</option>
                    @foreach ($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->user_id }}" data-nohp="{{ $pelanggan->no_hp }}"
                            data-nama="{{ $pelanggan->nama }}">
                            {{ $pelanggan->nama }} - {{ $pelanggan->no_hp }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" id="nama" name="nama">

                <!-- Nomor Telepon -->
                <div class="mb-3">
                    <label for="no_hp">Nomor Telepon</label>
                    <input type="text" id="no_hp" class="form-control" name="no_hp"
                        placeholder="Masukkan Nomor Telepon" required>
                </div>

                <!-- Jenis Kelamin -->
                <div class="mb-3">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option selected disabled>Pilih Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <!-- Tujuan -->
                <div class="mb-3">
                    <label for="kota_tujuan">Tujuan</label>
                    <select class="form-select" name="kota_tujuan" id="kota_tujuan" required>
                        <option selected disabled>Pilih Tujuan</option>
                        <option value="Duri">Duri</option>
                        <option value="Pekanbaru">Pekanbaru</option>
                        <option value="Padang">Padang</option>
                    </select>
                </div>

                <!-- Alamat Jemput -->
                <div class="mb-3">
                    <label for="alamat-jemput">Masukkan Alamat Jemput</label>
                    <input type="text" class="form-control" name="alamat_jemput" placeholder="Masukkan Alamat Jemput"
                        required>
                </div>

                <!-- Alamat Antar -->
                <div class="mb-3">
                    <label for="alamat-antar">Masukkan Alamat Antar</label>
                    <input type="text" class="form-control" name="alamat_antar" placeholder="Masukkan Alamat Antar"
                        required>
                </div>

                <!-- Tanggal Berangkat -->
                <div class="mb-3">
                    <label for="tanggal">Tanggal Berangkat</label>
                    <input type="date" class="form-control" name="tanggal" required placeholder="Tanggal Berangkat">
                </div>

                <!-- Jumlah Penumpang -->
                <div class="mb-3">
                    <label for="jumlah_penumpang">Jumlah Penumpang</label>
                    <input type="number" name="jumlah_penumpang" class="form-control" value="1" min="1"
                        required>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status">Status Pembayaran</label>
                    <select class="form-select" name="status" required>
                        <option selected disabled>Status Pembayaran</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Belum Lunas">Belum Lunas</option>
                    </select>
                </div>

                <!-- Pilih Travel -->
                <div class="mb-3 form-row">
                    <select class="form-select" name="jadwal_id" id="jadwal_id" required>
                        <option selected disabled>Pilih Travel</option>
                        @foreach ($jadwals as $jadwal)
                            <option value="{{ $jadwal->jadwal_id }}" data-tujuan="{{ $jadwal->kota_tujuan }}"
                                data-nopol="{{ $jadwal->mobil->nomor_polisi }}"
                                data-kapasitas="{{ $jadwal->mobil->kapasitas }}">
                                {{ $jadwal->mobil->nomor_polisi }} - {{ $jadwal->kota_asal }} ke
                                {{ $jadwal->kota_tujuan }}
                                ({{ formatIndonesianDate($jadwal->tanggal) }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="nomor_kursi" id="selectedSeatsInput">
                    <select class="form-select" id="nomor_kursi">
                        <option selected disabled>Pilih Kursi</option>
                    </select>
                    <div class="mt-2" id="selectedSeatsDisplay"></div>
                </div>

                <!-- Tombol Aksi -->
                <div class="button-container">
                    <a href="{{ route('data-pemesanan') }}" class="btn btn-secondary mr-1">Kembali</a>
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
        document.getElementById('user_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const nama = selectedOption.getAttribute('data-nama');
            const no_hp = selectedOption.getAttribute('data-nohp');

            // Isi input hidden nama
            document.getElementById('nama').value = nama;

            // (Opsional) Auto-isi nomor HP jika kosong
            const noHpInput = document.getElementById('no_hp');
            if (noHpInput && !noHpInput.value) {
                noHpInput.value = no_hp;
            }
        });
    </script>
    <script>
        const tujuanSelect = document.getElementById('kota_tujuan');
        const jadwalSelect = document.getElementById('jadwal_id');
        const kursiSelect = document.getElementById('nomor_kursi');
        const jumlahPenumpangInput = document.querySelector('input[name="jumlah_penumpang"]');

        const selectedSeatsInput = document.getElementById('selectedSeatsInput');
        const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');

        let kapasitas = 0;
        let kursiTerpakai = [];
        let selectedSeats = [];

        const allOptions = [...jadwalSelect.options];
        const kursiTerpakaiData = @json($kursiTerpakai ?? []);

        tujuanSelect.addEventListener('change', () => {
            const tujuan = tujuanSelect.value;
            jadwalSelect.innerHTML = '<option selected disabled>Pilih Travel</option>';
            allOptions.forEach(opt => {
                if (opt.dataset?.tujuan === tujuan) {
                    jadwalSelect.appendChild(opt);
                }
            });
            kursiSelect.innerHTML = '<option selected disabled>Pilih Kursi</option>';
            selectedSeats = [];
            updateSelectedSeats();
        });

        jadwalSelect.addEventListener('change', () => {
            const selectedOption = jadwalSelect.selectedOptions[0];
            const jadwalId = selectedOption.value;
            kapasitas = parseInt(selectedOption.dataset.kapasitas);
            kursiTerpakai = kursiTerpakaiData[jadwalId] || [];
            selectedSeats = [];
            updateSelectedSeats();
            generateKursiOptions();
        });

        jumlahPenumpangInput.addEventListener('input', () => {
            generateKursiOptions();
        });

        // Pilih kursi satu per satu
        kursiSelect.addEventListener('change', () => {
            const selected = kursiSelect.value;
            if (!selectedSeats.includes(selected)) {
                if (selectedSeats.length < parseInt(jumlahPenumpangInput.value)) {
                    selectedSeats.push(selected);
                    updateSelectedSeats();
                    generateKursiOptions(); // regenerate to hide selected
                } else {
                    alert("Jumlah kursi yang dipilih melebihi jumlah penumpang.");
                }
            }
            kursiSelect.selectedIndex = 0; // reset dropdown
        });

        function generateKursiOptions() {
            const jumlah = parseInt(jumlahPenumpangInput.value || 1);
            kursiSelect.innerHTML = '<option selected disabled>Pilih Kursi</option>';

            for (let i = 1; i <= kapasitas; i++) {
                const iStr = i.toString();
                if (!kursiTerpakai.includes(iStr) && !selectedSeats.includes(iStr)) {
                    const opt = document.createElement('option');
                    opt.value = iStr;
                    opt.textContent = `Kursi ${i}`;
                    kursiSelect.appendChild(opt);
                }
            }
        }

        function updateSelectedSeats() {
            // Update input hidden
            selectedSeatsInput.value = selectedSeats.join(',');

            // Update tampilan
            selectedSeatsDisplay.innerHTML = '';
            selectedSeats.forEach(seat => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary me-1';
                badge.textContent = `Kursi ${seat}`;
                selectedSeatsDisplay.appendChild(badge);
            });
        }
    </script>
@endpush
