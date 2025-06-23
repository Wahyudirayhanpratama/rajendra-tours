@extends('layouts.master')

@section('title', 'Tambah Data Penumpang')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="m-0 font-weight-bold">Tambah Penumpang</h1>
                </div>
            </div>
        </section>

        <div class="container">
            <form action="{{ route('store-data-penumpang') }}" method="POST">
                @csrf

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
                @if ($errors->has('general_error'))
                    <div class="alert alert-danger">
                        {{ $errors->first('general_error') }}
                    </div>
                @endif

                <!-- Pilih Pelanggan -->
                <div class="mb-3">
                    <label for="user_id" class="form-label">Nama Pemesan</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option selected disabled>Pilih Pelanggan</option>
                        @foreach ($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->user_id }}" data-nama="{{ $pelanggan->nama }}"
                                data-nohp="{{ $pelanggan->no_hp }}">
                                {{ $pelanggan->nama }} - {{ $pelanggan->no_hp }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="nama" name="nama">
                </div>

                <!-- Nomor HP -->
                <div class="mb-3">
                    <label for="no_hp" class="form-label">Nomor Telepon</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control"
                        placeholder="Masukkan Nomor Telepon" readonly required>
                </div>

                <!-- Tujuan -->
                <div class="mb-3">
                    <label for="kota_tujuan" class="form-label">Tujuan</label>
                    <select id="kota_tujuan" class="form-select" required>
                        <option selected disabled>Pilih Tujuan</option>
                        @foreach ($jadwals as $jadwal)
                            <option value="{{ $jadwal->jadwal_id }}" data-tujuan="{{ $jadwal->kota_tujuan }}"
                                data-kapasitas="{{ $jadwal->mobil->kapasitas }}"
                                data-nopol="{{ $jadwal->mobil->nomor_polisi }}" data-tanggal="{{ $jadwal->tanggal }}"
                                data-jam="{{ $jadwal->jam_berangkat }}">
                                {{ $jadwal->kota_asal }} ke {{ $jadwal->kota_tujuan }}
                                ({{ formatIndonesianDate($jadwal->tanggal) }})
                                - {{ $jadwal->mobil->nomor_polisi }} - {{ formatJam($jadwal->jam_berangkat) }} WIB
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="kota_tujuan" id="kota_tujuan">
                    <input type="hidden" name="jam_berangkat" id="jam_berangkat">
                    <input type="hidden" name="nomor_polisi" id="nomor_polisi">
                    <input type="hidden" name="jadwal_id" id="jadwal_id">
                </div>

                <!-- Jenis Kelamin -->
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option disabled selected>Pilih Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <!-- Jumlah Penumpang -->
                <div class="mb-3">
                    <label for="jumlah_penumpang" class="form-label">Jumlah Penumpang</label>
                    <input type="number" name="jumlah_penumpang" id="jumlah_penumpang" class="form-control" value="1"
                        min="1" required>
                    <div class="text-muted" id="kursi_tersedia_text"></div>
                </div>

                <!-- Nomor Kursi -->
                <div class="mb-3">
                    <label for="nomor_kursi" class="form-label">Nomor Kursi</label>
                    <select class="form-select" id="nomor_kursi">
                        <option selected disabled>Pilih Kursi</option>
                    </select>
                    <input type="hidden" name="nomor_kursi" id="selectedSeatsInput">
                    <div class="mt-2" id="selectedSeatsDisplay"></div>
                </div>

                <!-- Alamat Jemput -->
                <div class="mb-3">
                    <label class="form-label">Alamat Jemput</label>
                    <input type="text" name="alamat_jemput" class="form-control" required>
                </div>

                <!-- Alamat Antar -->
                <div class="mb-3">
                    <label class="form-label">Alamat Antar</label>
                    <input type="text" name="alamat_antar" class="form-control" required>
                </div>

                <!-- Tombol -->
                <div class="button-container">
                    <a href="{{ route('data.penumpang') }}" class="btn btn-secondary mr-1">Kembali</a>
                    <button type="submit" class="btn btn-pp text-white">Simpan</button>
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
        document.getElementById('user_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const nama = selectedOption.getAttribute('data-nama');
            const no_hp = selectedOption.getAttribute('data-nohp');

            // Isi nama hidden
            document.getElementById('nama').value = nama;

            // Auto update nomor HP setiap kali user berubah
            const noHpInput = document.getElementById('no_hp');
            if (noHpInput) {
                noHpInput.value = no_hp;
            }
        });
    </script>
    <script>
        const pelSelect = document.getElementById('user_id');
        const noHpInput = document.getElementById('no_hp');
        const namaInput = document.getElementById('nama');
        const jadwalSelect = document.getElementById('kota_tujuan');
        const nomorPolisiInput = document.getElementById('nomor_polisi');
        const jamInput = document.getElementById('jam_berangkat');
        const jumlahInput = document.getElementById('jumlah_penumpang');
        const kursiSelect = document.getElementById('nomor_kursi');
        const selectedSeatsInput = document.getElementById('selectedSeatsInput');
        const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');
        const pemesananInput = document.getElementById('pemesanan_id');
        const kursiTersediaText = document.getElementById('kursi_tersedia_text');
        const jadwalIdInput = document.getElementById('jadwal_id');

        const kursiTerpakaiData = @json($kursiTerpakai ?? []);
        let kapasitas = 0;
        let kursiTerpakai = [];
        let selectedSeats = [];

        pelSelect?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            noHpInput.value = selected.getAttribute('data-nohp');
            namaInput.value = selected.getAttribute('data-nama');
        });

        jadwalSelect?.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            kapasitas = parseInt(opt.getAttribute('data-kapasitas'));
            const jadwalId = opt.value;

            kursiTerpakai = kursiTerpakaiData[jadwalId] || [];
            const kursiTersisa = kapasitas - kursiTerpakai.length;

            if (kursiTersediaText) {
                kursiTersediaText.textContent = `Sisa kursi tersedia: ${kursiTersisa}`;
            }
            if (jamInput) {
                const jam = opt.getAttribute('data-jam');
                jamInput.value = jam || '-';
            }

            jumlahInput.max = kursiTersisa;
            jumlahInput.value = Math.min(jumlahInput.value, kursiTersisa);

            jadwalIdInput.value = jadwalId;
            nomorPolisiInput.value = opt.getAttribute('data-nopol') || '-';

            selectedSeats = [];
            updateSelectedSeats();
            generateKursiOptions();
        });

        jumlahInput?.addEventListener('input', () => {
            const max = parseInt(jumlahInput.max || kapasitas);
            if (parseInt(jumlahInput.value) > max) {
                alert(`Jumlah penumpang melebihi sisa kursi (${max}).`);
                jumlahInput.value = max;
            }
            generateKursiOptions();
        });

        kursiSelect?.addEventListener('change', function() {
            const selected = this.value;
            if (!selectedSeats.includes(selected)) {
                if (selectedSeats.length < parseInt(jumlahInput.value)) {
                    selectedSeats.push(selected);
                    updateSelectedSeats();
                    generateKursiOptions();
                } else {
                    alert('Jumlah kursi melebihi jumlah penumpang.');
                }
            }
            this.selectedIndex = 0;
        });

        function generateKursiOptions() {
            kursiSelect.innerHTML = '<option selected disabled>Pilih Kursi</option>';
            for (let i = 1; i <= kapasitas; i++) {
                const seat = i.toString();
                if (!kursiTerpakai.includes(seat) && !selectedSeats.includes(seat)) {
                    const opt = document.createElement('option');
                    opt.value = seat;
                    opt.textContent = `Kursi ${seat}`;
                    kursiSelect.appendChild(opt);
                }
            }
        }

        function updateSelectedSeats() {
            selectedSeatsInput.value = selectedSeats.join(',');
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
