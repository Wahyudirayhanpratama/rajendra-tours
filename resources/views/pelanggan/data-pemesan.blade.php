@extends('layouts.master6')

@section('title', 'Data Pemesanan')

@section('content')
    <!-- Loader -->
    <div id="loader">
        <div class="spinner-border text-light" role="status"></div>
    </div>
    <!-- App Header -->
    <div class="loginbg bg-po">
        <div class="left">
            <a href="#" class="headerButton goBack">
                <i class="uil uil-angle-left-b fs-25 text-white"></i>
            </a>
        </div>
        <div class="section">
            <div class="mb-1 text-center text-white">
                <div>{{ $cityfrom }} – {{ $cityto }}</div>
                <div class="fs-6 fw-bold">{{ formatIndonesianDate($tanggal) }}</div>
            </div>
        </div>
    </div>
    <!-- * App Header -->

    <!-- Container utama dengan background biru muda -->
    <div id="formDataPemesan" class="section p-3" style="min-height: 100vh; padding-top: 20px;">

        <div class="card p-3" style="background-color: transparent; border: none; box-shadow: none;">
            <h5 class="fw-bold mb-3">Data Pemesan</h5>

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

            <form action="{{ route('penumpang.store') }}" method="POST">
                @csrf

                {{-- Hidden jadwal_id, jumlah_penumpang, dan total_harga --}}
                <input type="hidden" name="jadwal_id" value="{{ $jadwal_id }}">
                <input type="hidden" name="total_harga" value="{{ $total_harga }}">

                <!-- Nama Pemesan -->
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user text-dark icon-regist"></i>
                        </span>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Pemesan"
                            value="{{ Auth::guard('pelanggan')->user()->nama ?? '' }}">
                    </div>
                </div>

                <!-- Nomor Telepon -->
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-phone text-dark icon-regist"></i>
                        </span>
                        <input type="text" name="no_hp" class="form-control rounded-end" placeholder="Nomor Telepon"
                            value="{{ Auth::guard('pelanggan')->user()->no_hp ?? '' }}">
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-venus-mars text-dark icon-regist"></i>
                        </span>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Pilih Tempat Duduk -->
                <input type="hidden" id="jumlah_penumpang_input" name="jumlah_penumpang" value="{{ $jumlah_penumpang }}">
                <input type="hidden" id="kapasitas" name="kapasitas" value="{{ $kapasitas }}">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <img class="icon-regist" src="https://img.icons8.com/ios-filled/50/000000/car-seat.png"
                                width="22">
                        </span>
                        <input type="text" id="inputTempatDuduk" class="form-control" placeholder="Pilih Kursi" readonly>
                        <span class="input-group-text bg-white">
                            <i class="fas fa-chevron-right text-dark icon-regist"></i>
                        </span>
                    </div>
                </div>
                <!-- Kontainer hasil kursi terpilih -->
                <div id="selectedSeatsContainer" class="mb-3"></div>

                <!-- Alamat Jemput -->
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-location-dot text-dark icon-regist"></i>
                        </span>
                        <input type="text" name="alamat_jemput" class="form-control rounded-end"
                            placeholder="Alamat Jemput" required>
                    </div>
                </div>

                <!-- Alamat Antar -->
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-map-marker-alt text-dark icon-regist"></i>
                        </span>
                        <input type="text" name="alamat_antar" class="form-control rounded-end"
                            placeholder="Alamat Antar" required>
                    </div>
                </div>

                <!-- Syarat dan Ketentuan -->
                <div class="alert"
                    style="border: 2px solid #000080; color: #000; font-size: 13px; padding: 10px; margin-top: 20px;">
                    <span style="color: #b30000; font-weight: 700;">PERHATIAN</span><br>
                    Syarat & ketentuan pemesanan yang berlaku:<br>
                    1. Reservasi dan pembatalan dapat dilakukan 3 jam sebelum berangkat.<br>
                    2. Jika melakukan pembatalan dilakukan setelah 3 jam sebelum berangkat, maka tidak ada pengembalian dana
                    (NO REFUND & NO RESCHEDULE)
                </div>

                <!-- Tombol -->
                <div class="d-grid mt-4">
                    <button type="submit" class="btn bg-po text-white">
                        Pilih Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Login -->
    @guest
        <div class="modal fade action-sheet inset" id="actionSheetInsetLogin" tabindex="-1" role="dialog"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">LOGIN</h5>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-2 fs-13 text-dark">
                            Untuk dapat melakukan pemesanan tiket, Anda harus login terlebih dahulu
                        </div>
                        <form id="loginFormModal">
                            @csrf
                            <!-- Nomor HP -->
                            <div class="form-group mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user text-dark icon-regist"></i></span>
                                    <input type="text" name="no_hp" class="form-control" placeholder="Masukkan No HP"
                                        required>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="form-group mb-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-lock text-dark icon-regist"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control border-start-0"
                                        placeholder="Masukkan Password" required>
                                </div>
                            </div>

                            <button type="submit" id="loginbtnModal" class="btn btn-po btn-block w-100">LOGIN</button>
                            <!-- Tombol Kembali -->
                            <a href="{{ route('jadwal.cari') }}" class="btn btn-secondary mt-2 w-100">Kembali ke Jadwal</a>
                        </form>

                        <div class="text-center mt-2" style="font-size: small">
                            Belum punya akun? <form id="redirect-register-form" action="{{ route('register.redirect') }}"
                                method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="from" value="data-pemesan">
                                <input type="hidden" name="jadwal_id" value="{{ $jadwal_id }}">
                                <input type="hidden" name="cityfrom" value="{{ $cityfrom }}">
                                <input type="hidden" name="cityto" value="{{ $cityto }}">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <input type="hidden" name="jumlah_penumpang" value="{{ $jumlah_penumpang }}">
                            </form>

                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('redirect-register-form').submit();">
                                Daftar Sekarang
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endguest

    <!-- Modal Popup Pilih Kursi -->
    <div class="modal fade" id="actionSheetSeat" tabindex="-1" aria-labelledby="actionSheetSeatLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Informasi Kursi -->
                        <div class="col-4 border-end text-center py-3">
                            <div class="mb-3 fw-bold text-po">INFORMASI KURSI</div>
                            <div class="mb-2 mt-4">
                                <img src="https://img.icons8.com/emoji/48/bust-in-silhouette.png" width="32"
                                    alt="Supir">
                                <div class="fs-12 mt-1">Kursi Supir</div>
                            </div>
                            <div class="mb-2 mt-4">
                                <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png" width="32"
                                    alt="Kursi Tersedia">
                                <div class="fs-12 mt-1">Kursi yang Tersedia</div>
                            </div>
                            <div class="mb-2 mt-4">
                                <img src="https://img.icons8.com/ios-filled/50/888888/car-seat.png" width="32"
                                    style="opacity: 0.3;" alt="Tidak Tersedia">
                                <div class="fs-12 mt-1">Kursi Tidak Tersedia</div>
                            </div>
                        </div>

                        <!-- Pilihan Tempat Duduk -->
                        <div class="col-8 py-3 px-3">
                            <div class="text-center fw-bold mb-2 text-po">PILIH TEMPAT DUDUK</div>
                            <div class="row text-center" id="seatmapWrapper">
                                <div class="col-6 mb-3">
                                    <button class="btn seat-btn" data-seat="1">
                                        <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png"
                                            width="32"><br>1
                                    </button>
                                </div>
                                <div class="col-6 mb-3">
                                    <button class="btn seat-btn driver" disabled>
                                        <img src="https://img.icons8.com/emoji/48/bust-in-silhouette.png"
                                            width="30"><br>S
                                    </button>
                                </div>
                                <div class="col-6 mb-3">
                                    <button class="btn seat-btn" data-seat="2">
                                        <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png"
                                            width="32"><br>2
                                    </button>
                                </div>
                                <div class="col-6 mb-3">
                                    <button class="btn seat-btn" data-seat="3">
                                        <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png"
                                            width="32"><br>3
                                    </button>
                                </div>
                                <div class="col-6 mb-3">
                                    <button class="btn seat-btn" data-seat="4">
                                        <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png"
                                            width="32"><br>4
                                    </button>
                                </div>
                                <div class="col-6 mb-3">
                                    <button class="btn seat-btn" data-seat="5">
                                        <img src="https://img.icons8.com/ios-filled/50/000000/car-seat.png"
                                            width="32"><br>5
                                    </button>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button id="pilihSeat" class="btn btn-po w-100" data-bs-dismiss="modal"
                                    disabled>PILIH</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('headerspwa')
    <style>
        .loginbg {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            width: 100%;
            height: 13vh;
            padding-top: 20px;
            position: relative;
        }

        .left {
            position: absolute;
            top: 25px;
            /* Atur jarak dari atas */
            left: 16px;
            /* Atur jarak dari kiri */
            /* Styling lain untuk tombol back */
        }

        .city-schedule {
            padding: 0 8px;
            z-index: 1;
            position: relative;
        }

        .line-dot {
            position: absolute;
            border-bottom: 2px dotted #ffffff;
            top: 0;
            width: -webkit-fill-available;
            bottom: 26px;
            margin: 0px 20px;
        }

        .bg-po {
            background-color: #000080 !important;
        }

        .bg-blue {
            background-color: #92B6F8 !important
        }

        .input-group-text {
            padding-left: 0.5rem;
            /* Tambahkan ruang di kiri ikon */
            padding-right: 0.5rem;
            /* Tambahkan ruang di kanan ikon */
        }

        /* New Styles for the Form */
        .input-field {
            border-left: none !important;
        }

        .input-group {
            border-radius: 0.375rem;
            overflow: hidden;
            /* Tambahkan ini untuk mencegah border overlap */
        }

        .input-group-text {
            background-color: white;
            border-right: none !important;
            padding: 0.375rem 0.75rem;
        }

        .input-group:focus-within {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-control {
            border-left: none !important;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: #ced4da;
        }

        /* Menghilangkan border radius yang tidak perlu */
        .input-group-text:first-child {
            border-top-left-radius: 0.375rem !important;
            border-bottom-left-radius: 0.375rem !important;
        }

        .form-control:last-child {
            border-top-right-radius: 0.375rem !important;
            border-bottom-right-radius: 0.375rem !important;
        }

        .icon-regist {
            margin-left: 10px;
        }

        .seat-btn {
            background-color: white;
            border: 2px solid #000080;
            color: #000080;
            width: 65px;
            height: 65px;
            border-radius: 10px;
        }

        .seat-btn.selected {
            background-color: #7CFC00 !important;
            /* hijau untuk kursi terpilih */
            color: white;
            border-color: #7CFC00;
        }

        .seat-btn.driver {
            background-color: white;
            border: 2px solid #000080;
            color: black;
            width: 60px;
            height: 60px;
            border-radius: 10px;
        }

        .seat-btn:disabled {
            background-color: #ccc;
            opacity: 0.5;
            cursor: not-allowed;
        }

        .fs-12 {
            font-size: 12px;
        }

        .fs-2 {
            font-size: 24px;
        }

        .text-po {
            color: #000080;
        }
    </style>
    <style>
        .modal-content {
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-custom {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 15px 20px;
            position: relative;
            top: -30px;
            margin-bottom: -30px;
        }

        .modal-header-custom {
            background-color: #000080;
            color: white;
            padding: 20px;
            height: 150px;
            width: 100%;
        }

        .logo-bank img,
        .logo-qris img {
            height: 20px;
            margin-right: 10px;
            margin-bottom: 5px;
        }

        .payment-option {
            cursor: pointer;
            transition: 0.2s;
            padding: 10px;
            border-radius: 8px;
        }

        .payment-option:hover {
            background-color: #f1f1f1;
        }

        .timer-text {
            font-size: 13px;
            color: #1b1eae;
        }
    </style>
@endpush

@push('scriptspwa')
    @guest('pelanggan')
        @if (session('show_login_popup'))
            <script>
                $(document).ready(function() {
                    $('#actionSheetInsetLogin').modal({
                        backdrop: 'static',
                        keyboard: false
                    }).modal('show');
                });
            </script>
        @endif
        <script>
            $(document).ready(function() {
                $('#actionSheetInsetLogin').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');

                $('#loginFormModal').on('submit', function(e) {
                    e.preventDefault();

                    const $btn = $('#loginbtnModal');
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

                    $.ajax({
                        url: '{{ route('login.pelanggan.ajax') }}',
                        type: 'POST',
                        data: $(this).serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Login!',
                                    text: 'Selamat datang kembali.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Refresh setelah alert selesai
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Login gagal, coba lagi.'
                                });
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Terjadi kesalahan. Silakan coba lagi.';
                            if (xhr.responseJSON?.message) {
                                msg = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: msg
                            });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html('LOGIN');
                        }
                    });
                });
            });
        </script>
    @endguest

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const jumlahPenumpang = parseInt(document.getElementById("jumlah_penumpang_input").value || 1);
            const kapasitasMobil = parseInt(document.getElementById("kapasitas").value || 5);
            const kursiTerpakai = @json($kursi_terpakai);
            let selectedSeats = [];

            const inputTempatDuduk = document.getElementById("inputTempatDuduk");
            const pilihSeatButton = document.getElementById("pilihSeat");
            const selectedSeatsContainer = document.getElementById("selectedSeatsContainer");
            const seatButtons = document.querySelectorAll(".seat-btn:not(.driver)");

            const seatModalEl = document.getElementById('actionSheetSeat');
            const seatModal = new bootstrap.Modal(seatModalEl);

            // Reset saat buka modal
            inputTempatDuduk.addEventListener("click", () => {
                selectedSeats = []; // reset array
                pilihSeatButton.disabled = true;

                seatButtons.forEach(button => {
                    const seatNumber = parseInt(button.dataset.seat);
                    const seatStr = button.dataset.seat;

                    button.classList.remove("selected");

                    if (seatNumber <= kapasitasMobil) {
                        if (kursiTerpakai.includes(seatStr)) {
                            // Jika kursi sudah dipakai sebelumnya
                            button.disabled = true;
                            button.classList.add("disabled");
                            button.style.opacity = 0.3;
                            button.title = "Sudah dipesan";
                        } else {
                            button.disabled = false;
                            button.classList.remove("disabled");
                            button.style.opacity = 1;
                            button.title = "";
                        }
                    } else {
                        // kursi di luar kapasitas
                        button.disabled = true;
                        button.classList.add("disabled");
                        button.style.opacity = 0.3;
                    }
                });

                seatModal.show();
            });

            // Klik kursi
            seatButtons.forEach(button => {
                button.addEventListener("click", () => {
                    if (button.disabled) return;

                    const seat = button.getAttribute("data-seat");

                    if (button.classList.contains("selected")) {
                        // Batalkan jika diklik ulang
                        button.classList.remove("selected");
                        selectedSeats = selectedSeats.filter(s => s !== seat);
                    } else {
                        if (selectedSeats.length < jumlahPenumpang) {
                            // Tambah jika belum penuh
                            button.classList.add("selected");
                            selectedSeats.push(seat);
                        } else {
                            // Jika sudah penuh, ganti kursi pertama
                            const removedSeat = selectedSeats.shift(); // hapus kursi pertama
                            const removedButton = [...seatButtons].find(btn => btn.dataset.seat ===
                                removedSeat);
                            if (removedButton) removedButton.classList.remove("selected");

                            // Tambahkan kursi baru
                            selectedSeats.push(seat);
                            button.classList.add("selected");
                        }
                    }

                    pilihSeatButton.disabled = selectedSeats.length !== jumlahPenumpang;
                });
            });
            pilihSeatButton.addEventListener("click", () => {
                // Hapus input sebelumnya hanya dari container
                selectedSeatsContainer.querySelectorAll('input[name="nomor_kursi[]"]').forEach(el => el
                    .remove());
                selectedSeatsContainer.innerHTML = "";

                selectedSeats.forEach(seat => {
                    const label = document.createElement("div");
                    label.className = "badge bg-po me-1";
                    label.innerText = "Kursi " + seat;

                    const hidden = document.createElement("input");
                    hidden.type = "hidden";
                    hidden.name = "nomor_kursi[]";
                    hidden.value = seat;

                    selectedSeatsContainer.appendChild(label);
                    selectedSeatsContainer.appendChild(hidden);
                });

                inputTempatDuduk.value = selectedSeats.join(", ");
                seatModal.hide();
            });
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.log('ServiceWorker registration failed:', error);
                });
        }
    </script>
@endpush
