@extends('layouts.master3')

@section('title', 'Cari Jadwal')

@section('content')

    <!-- logo -->
    <div class="container text-center">
        <div class="mb-5">
            <img class="img-fluid" src="{{ asset('storage/logo_rajendra.png') }}" style="width: 200px;">
        </div>
    </div>

    <div class="section">
        <div class="card">

            <div class="card-body">
                <form id="formCariTiket" action="{{ route('jadwal.cari') }}" method="GET">
                    <div class="col-12 mt-2">
                        <div class="text-dark">Kota Keberangkatan</div>
                        <div class="p-1 radius-1">
                            <x-forms.input id="cityfromlabel" class="form-control" readonly
                                placeholder="Pilih Kota Keberangkatan" onclick="tampilkanKotaKeberangkatan()">

                                <input type="hidden" id="cityfrom" name="cityfrom">
                                <i class="input-icon">
                                    <i class="uil uil-map-pin-alt fs-25 text-dark"></i>
                                </i>
                            </x-forms.input>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="text-dark">Kota Tujuan</div>
                        <div class="bg-white p-1 radius-1">
                            <x-forms.input id="citytolabel" readonly placeholder="Pilih Kota Tujuan"
                                onclick="tampilkanKotaTujuanManual()">

                                <input type="hidden" id="cityto" name="cityto">
                                <i class="input-icon">
                                    <i class="uil uil-map-marker fs-25 text-dark"></i>
                                </i>
                            </x-forms.input>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="text-dark">Tanggal Keberangkatan</div>
                        <div class="bg-white p-1 radius-1">
                            <div class="form-group searchbox2">
                                <input type="date" class="form-control fs-15" id="date" name="date"
                                    placeholder="Tanggal Keberangkatan" min="{{ date('Y-m-d') }}"
                                    value="{{ old('date', date('Y-m-d')) }}" required>
                                <i class="input-icon">
                                    <i class="uil uil-calendar-alt fs-25 text-dark"></i>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="text-dark">Penumpang</div>
                        <div class="passenger-selector">
                            <div class="passenger-icon">
                                <i class="uil uil-user"></i>
                            </div>
                            <div class="passenger-options">
                                <button type="button" class="active" onclick="selectPassenger(this, 1)">1</button>
                                <button type="button" onclick="selectPassenger(this, 2)">2</button>
                                <button type="button" onclick="selectPassenger(this, 3)">3</button>
                                <button type="button" onclick="selectPassenger(this, 4)">4</button>
                                <button type="button" onclick="selectPassenger(this, 5)">5</button>
                            </div>
                        </div>
                        <!-- Hidden input untuk dikirim ke server -->
                        <input type="hidden" id="jumlah_penumpang" name="jumlah_penumpang" value="1">
                    </div>
                    <div class="section mt-4 mb-1 col-12">
                        <button type="submit" class="btn btn-block btn-lg" style="background-color:#000080; color:white;">
                            Cari Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Search City -->
    <div class="modal fade" id="modalCity" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center px-3 py-2 mt-2 border-0 w-100">
                    <h5 class="modal-title fs-6 fw-bold m-0" id="modalTitle">Pilih Kota</h5>
                    <a href="#" data-bs-dismiss="modal">
                        <i class="uil uil-multiply fs-18 text-dark"></i>
                    </a>
                </div>
                <div class="modal-body pt-1 mt-3">
                    <div class="list-group" id="daftarkota">
                        <!-- Tombol kota akan ditampilkan dengan JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- * Modal Basic -->
@endsection

@push('headerspwa')
    <style>
        .loginbg {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            width: 100%;
            height: 50vh;
            border-bottom-left-radius: 60px;
            border-bottom-right-radius: 60px;
        }

        .bg-po {
            background-color: #92B6F8 !important;
        }

        .passenger-selector {
            display: flex;
            align-items: center;
            border: 1px solid black;
            padding: 10px;
            border-radius: 8px;
            gap: 10px;
            flex-wrap: wrap;
            /* fallback */
        }

        .passenger-icon {
            font-size: 24px;
            color: black;
            flex-shrink: 0;
        }

        .passenger-options {
            display: flex;
            flex: 1;
            justify-content: space-between;
            gap: 5px;
        }

        .passenger-options button {
            flex: 1;
            /* agar semua tombol lebar sama dan responsif */
            height: 36px;
            border-radius: 8px;
            border: 1px solid #001B79;
            background-color: white;
            color: #001B79;
            font-weight: 600;
        }

        .passenger-options button.active {
            background-color: #001B79;
            color: #fff;
        }

        .list-group-item-action:hover {
            background-color: #e9ecef !important;
            transform: scale(1.02);
            transition: 0.2s ease;
        }
    </style>
@endpush

@push('scriptspwa')
    <script>
        document.getElementById('formCariTiket').addEventListener('submit', function(e) {
            const cityFrom = document.getElementById('cityfrom').value;
            const cityTo = document.getElementById('cityto').value;
            const date = document.getElementById('date').value;
            const penumpang = document.getElementById('jumlah_penumpang').value;

            if (!cityFrom || !cityTo || !date || !penumpang) {
                e.preventDefault(); // hentikan submit form

                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap!',
                    text: 'Silakan lengkapi semua field terlebih dahulu.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Oke'
                });
            }
        });
    </script>
    <script>
        function selectPassenger(button, value) {
            document.querySelectorAll('.passenger-options button').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            document.getElementById('jumlah_penumpang').value = value;
        }
    </script>
    <script>
        const semuaKota = ["Duri", "Pekanbaru", "Padang"];

        function tampilkanKotaKeberangkatan() {
            const daftar = semuaKota.map(kota =>
                `<button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 rounded-3 shadow-sm border-0 mb-2"
                onclick="pilihKotaKeberangkatan('${kota}')"style="background-color: #f8f9fa;">
                <i class="uil uil-map-marker text-primary fs-5"></i>
            <span class="fw-semibold text-dark">${kota}</span>
            </button>`
            ).join("");
            document.getElementById("modalTitle").innerText = "Pilih Kota Keberangkatan";
            document.getElementById("daftarkota").innerHTML = daftar;
            const modal = new bootstrap.Modal(document.getElementById('modalCity'));
            modal.show();
        }

        function tampilkanKotaTujuanManual() {
            const asal = document.getElementById("cityfrom").value;

            if (!asal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kota Keberangkatan!',
                    text: 'Silakan pilih kota keberangkatan terlebih dahulu.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Oke'
                });
                return;
            }

            const tujuan = semuaKota.filter(kota => kota !== asal);
            const daftar = tujuan.map(kota =>
                `<button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 rounded-3 shadow-sm border-0 mb-2"
                onclick="pilihKotaTujuan('${kota}')"style="background-color: #f8f9fa;">
                <i class="uil uil-map-marker text-primary fs-5"></i>
            <span class="fw-semibold text-dark">${kota}</span>
            </button>`
            ).join("");
            document.getElementById("modalTitle").innerText = "Pilih Kota Tujuan";
            document.getElementById("daftarkota").innerHTML = daftar;
            const modal = new bootstrap.Modal(document.getElementById('modalCity'));
            modal.show();
        }

        function pilihKotaKeberangkatan(kota) {
            document.getElementById("cityfrom").value = kota;
            document.getElementById("cityfromlabel").value = kota;
            bootstrap.Modal.getInstance(document.getElementById('modalCity')).hide();
        }

        function pilihKotaTujuan(kota) {
            document.getElementById("cityto").value = kota;
            document.getElementById("citytolabel").value = kota;
            bootstrap.Modal.getInstance(document.getElementById('modalCity')).hide();
        }
    </script>
@endpush
