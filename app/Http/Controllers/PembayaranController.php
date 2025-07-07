<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function preview(Request $request)
    {
        // Ambil data dari jadwal
        $jadwal = Jadwal::with('mobil')->where('jadwal_id', $request->jadwal_id)->firstOrFail();
        // Simpan sementara ke session atau teruskan ke view preview
        // Tidak ada Pemesanan::create di sini lagi
        session([
            'preview_pemesanan' => [
                'cityfrom' => session('cityfrom'),
                'cityto' => session('cityto'),
                'tanggal' => session('tanggal'),
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'total_harga' => $request->total_harga,
                'jadwal_id' => $request->jadwal_id,
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_kursi' => $request->nomor_kursi,
                'alamat_jemput' => $request->alamat_jemput,
                'alamat_antar' => $request->alamat_antar,
                'nomor_polisi' => $jadwal->mobil->nomor_polisi,
            ]
        ]);

        return view('pelanggan.preview_pemesanan', [ // Buat view preview_pemesanan jika perlu
            'cityfrom' => session('cityfrom'),
            'cityto' => session('cityto'),
            'tanggal' => session('tanggal'),
            'jumlah_penumpang' => $request->jumlah_penumpang,
            'total_harga' => $request->total_harga,
            'jadwal' => $jadwal,
            'request' => $request->all(),
        ]);
    }
    // Method baru untuk menampilkan halaman pembayaran dan membuat Snap Token
    // Ini akan dipanggil setelah pemesanan dibuat di PemesananController
    public function showPaymentPage($pemesanan_id, MidtransService $midtrans)
    {
        try {
            $pemesanan = Pemesanan::with('user', 'jadwal')->findOrFail($pemesanan_id);

            // Siapkan payload untuk Midtrans Snap dari data pemesanan yang sudah ada
            $payload = [
                'transaction_details' => [
                    'order_id' => $pemesanan->kode_booking, // PENTING: Gunakan kode_booking dari pemesanan
                    'gross_amount' => $pemesanan->total_harga,
                ],
                'customer_details' => [
                    'first_name' => $pemesanan->user->nama, // Asumsi user relasi ke Pemesanan
                    'email' => $pemesanan->user->email ?? 'dummy@mail.com',
                    // Jika nomor telepon disimpan di Pemesanan atau Penumpang, ambil dari sana
                    // 'phone' => $pemesanan->penumpang->first()->no_hp ?? '081234567890',
                ],
                'item_details' => [
                    [
                        'id' => $pemesanan->jadwal->jadwal_id,
                        'price' => (int) $pemesanan->jadwal->harga,
                        'quantity' => (int) $pemesanan->jumlah_penumpang,
                        'name' => 'Tiket Bus ' . $pemesanan->jadwal->cityfrom . ' - ' . $pemesanan->jadwal->cityto,
                    ]
                ]
            ];

            $snapToken = $midtrans->createSnapTransaction($payload);

            return view('pelanggan.bayar', [
                'cityfrom' => $pemesanan->jadwal->cityfrom,
                'cityto' => $pemesanan->jadwal->cityto,
                'tanggal' => $pemesanan->jadwal->tanggal, // Asumsi ada kolom tanggal di jadwal
                'jumlah_penumpang' => $pemesanan->jumlah_penumpang,
                'total_harga' => $pemesanan->total_harga,
                'kode_booking' => $pemesanan->kode_booking,
                'snapToken' => $snapToken,
                'pemesanan' => $pemesanan, // Kirim objek pemesanan lengkap jika diperlukan
            ]);
        } catch (\Exception $e) {
            Log::error('Error displaying payment page: ' . $e->getMessage(), ['exception' => $e, 'pemesanan_id' => $pemesanan_id]);
            return back()->withErrors('Terjadi kesalahan saat menampilkan halaman pembayaran: ' . $e->getMessage());
        }
    }
}
