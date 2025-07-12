<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PemilikController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Middleware\RedirectIfNotPelanggan;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotPemilik;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PenumpangController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\DetailTiketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\SuratJalanController;

Route::post('pelanggan.jadwal/set-tanggal', function (\Illuminate\Http\Request $request) {
    $tanggal = $request->input('date');
    $hari = \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');
    return response($hari);
});

Route::resource('users', UserController::class);

Route::get('/register', [AuthController::class, 'showPelangganRegisterForm'])->name('register.pelanggan');
Route::post('/register', [UserController::class, 'registerPelanggan'])->name('register.pelanggan.submit');
Route::post('/login-pelanggan', [AuthController::class, 'loginPelanggan'])->name('login.pelanggan.submit');
Route::get('/login-pelanggan', [AuthController::class, 'showPelangganLoginForm'])->name('login.pelanggan');
Route::post('/login-pelanggan-ajax', [AuthController::class, 'loginAjax'])->name('login.pelanggan.ajax');
Route::post('/logout-pelanggan', [AuthController::class, 'logoutPelanggan'])->name('logout.pelanggan');

Route::resource('jadwals', JadwalController::class);
Route::get('/jadwal/cari/{tanggal?}', [JadwalController::class, 'cari'])->name('jadwal.cari');
Route::get('/', [JadwalController::class, 'showCari'])->name('cari-jadwal');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Middleware manual cek session agar tidak bisa akses dashboard kalau belum login
Route::middleware(['auth:admin', RedirectIfNotAdmin::class])->group(function () {
    //Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'dashboard'])->name('dashboard.admin');

    //CRUD Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'dataPelanggan'])->name('data-pelanggan');
    Route::get('/pelanggan/tambah', [PelangganController::class, 'createPelanggan'])->name('tambah-data-pelanggan');
    Route::post('/pelanggan/tambah', [PelangganController::class, 'storePelanggan'])->name('store-pelanggan');
    Route::get('/pelanggan/edit/{id}', [PelangganController::class, 'editPelanggan'])->name('edit-data-pelanggan');
    Route::post('/pelanggan/update/{id}', [PelangganController::class, 'updatePelanggan'])->name('update-data-pelanggan');
    Route::delete('/pelanggan/hapus/{id}', [PelangganController::class, 'deletePelanggan'])->name('hapus-data-pelanggan');
    // CRUD Mobil
    Route::get('/mobil', [MobilController::class, 'dataMobil'])->name('data-mobil');
    Route::get('/mobil/tambah', [MobilController::class, 'createMobil'])->name('tambah-data-mobil');
    Route::post('/mobil/tambah', [MobilController::class, 'storeMobil'])->name('store-data-mobil');
    Route::get('/mobil/edit/{id}', [MobilController::class, 'editMobil'])->name('edit-data-mobil');
    Route::put('/mobil/update/{id}', [MobilController::class, 'updateMobil'])->name('update-data-mobil');
    Route::delete('/mobil/hapus/{id}', [MobilController::class, 'deleteMobil'])->name('hapus-data-mobil');
    //CRUD Jadwal
    Route::get('/jadwal', [JadwalController::class, 'jadwalKeberangkatan'])->name('jadwal-keberangkatan');
    Route::get('/jadwal/tambah', [JadwalController::class, 'createJadwal'])->name('tambah-jadwal-keberangkatan');
    Route::post('/jadwal/tambah', [JadwalController::class, 'storeJadwal'])->name('store-jadwal-keberangkatan');
    Route::get('/jadwal/edit/{id}', [JadwalController::class, 'editJadwal'])->name('edit-jadwal-keberangkatan');
    Route::put('/jadwal/update/{id}', [JadwalController::class, 'updateJadwal'])->name('update-jadwal-keberangkatan');
    Route::delete('/jadwal/hapus/{id}', [JadwalController::class, 'deleteJadwal'])->name('hapus-jadwal-keberangkatan');
    //Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'dashboard'])->name('dashboard.admin');
    //CRUD Pemesanan
    Route::get('/data-pemesanan', [PemesananController::class, 'pemesanan'])->name('data-pemesanan');
    Route::post('/data-pemesanan/store', [PemesananController::class, 'storePemesanan'])->name('store-data-pemesanan');
    // Route::get('/data-pemesanan/tambah', [PemesananController::class, 'createPemesanan'])->name('tambah-data-pemesanan');
    // Route::get('/data-pemesanan/edit/{id}', [PemesananController::class, 'editPemesanan'])->name('edit-data-pemesanan');
    // Route::put('/data-pemesanan/update/{id}', [PemesananController::class, 'updatePemesanan'])->name('update-data-pemesanan');
    // Route::delete('/data-pemesanan/hapus/{id}', [PemesananController::class, 'destroyPemesanan'])->name('hapus-data-pemesanan');
    //CRUD Penumpang
    Route::get('/data-penumpang', [PenumpangController::class, 'index'])->name('data.penumpang');
    Route::get('/data-penumpang/tambah', [PenumpangController::class, 'createPenumpang'])->name('tambah-data-penumpang');
    Route::post('/data-penumpang/store', [PenumpangController::class, 'storePenumpang'])->name('store-data-penumpang');
    Route::get('/data-penumpang/edit/{id}', [PenumpangController::class, 'editPenumpang'])->name('edit-data-penumpang');
    Route::put('/data-penumpang/update/{id}', [PenumpangController::class, 'updatePenumpang'])->name('update-data-penumpang');
    Route::delete('/data-penumpang/hapus/{id}', [PenumpangController::class, 'destroyPenumpang'])->name('hapus-data-penumpang');
    //Surat Jalan
    Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat.jalan');
    Route::get('/cetak-surat-jalan/{id}', [SuratJalanController::class, 'cetak'])->name('cetak.surat-jalan');
});

Route::middleware(RedirectIfNotPemilik::class)->group(function () {
    //Dashboard
    Route::get('/dashboard/pemilik', [DashboardController::class, 'dashboardPemilik'])->name('dashboard.pemilik');
    //Laporan Transaksi
    Route::get('/laporan-transaksi', [PemilikController::class, 'laporanTransaksi'])->name('laporan.transaksi');
});

Route::middleware(RedirectIfNotPelanggan::class)->group(function () {
    //Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
    //Tiket
    Route::get('/tiket', [TiketController::class, 'index'])->name('tiket');
    //Detail Tiket
    Route::get('/detail-tiket/{id}', [TiketController::class, 'show'])->name('detail.tiket');
    //Batalkan Tiket
    Route::post('/batalkan-tiket/{id}', [DetailTiketController::class, 'batalkan'])->name('tiket.batalkan');
    //Input data pemesan
    Route::get('/penumpang', [PenumpangController::class, 'showTiket'])->name('penumpang.show');
    Route::post('/penumpang/store', [PenumpangController::class, 'store'])->name('penumpang.store');
    //Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    //Bayar
    Route::post('/pembayaran/preview', [PembayaranController::class, 'preview'])->name('pembayaran.preview');
    Route::get('/pembayaran/{pemesanan_id}', [PembayaranController::class, 'showPaymentPage'])->name('pembayaran.show');
    Route::get('/bayar/{id}', [PemesananController::class, 'bayarTiket'])->name('bayar');
});
//Modal Popup Login Pada Data Pemesan Pelanggan
Route::get('/penumpang/create', [PenumpangController::class, 'create'])->name('penumpang.create');

Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification'])->name('midtrans.notification');

//sementara utk testing midtrans di postman
Route::get('/test-signature', function () {
    $orderId = 'BK-A07SGR';
    $grossAmount = '140000';
    $statusCode = '200';
    $serverKey = config('midtrans.server_key');

    $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

    return $signature;
});
