<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function bayar($pemesanan_id, MidtransService $midtrans)
    {
        $pemesanan = Pemesanan::findOrFail($pemesanan_id);

        $payload = [
            'transaction_details' => [
                'order_id' => $pemesanan->kode_booking,
                'gross_amount' => $pemesanan->total_harga,
            ],
            'customer_details' => [
                'first_name' => $pemesanan->user->nama,
                'email' => $pemesanan->user->email ?? 'dummy@email.com',
            ]
        ];

        $snapToken = $midtrans->createSnapTransaction($payload);

        return view('pelanggan.bayar', compact('snapToken'));
    }
    public function handleNotification(Request $request)
    {
        Log::info('MIDTRANS NOTIFICATION RECEIVED'); // Log awal untuk debugging

        // Ambil data dari request
        $data = $request->all();
        // Jika request body kosong, coba ambil dari php://input (untuk beberapa kasus Midtrans)
        if (empty($data)) {
            $rawInput = file_get_contents('php://input');
            $decodedInput = json_decode($rawInput, true);
            // Pastikan $data selalu berupa array, bahkan jika decoding gagal
            $data = is_array($decodedInput) ? $decodedInput : [];
        }

        Log::info('DATA RECEIVED:', $data); // Sekarang $data dijamin berupa array

        // Pastikan order_id ada dalam data notifikasi
        if (!isset($data['order_id'])) {
            Log::error('Order ID not found in Midtrans notification data.');
            return response()->json(['message' => 'Order ID not found'], 400);
        }

        // Cari pemesanan berdasarkan kode_booking (yang sama dengan order_id Midtrans)
        $pemesanan = Pemesanan::where('kode_booking', $data['order_id'])->first();

        if (!$pemesanan) {
            Log::error('Pemesanan not found for order_id: ' . $data['order_id']);
            return response()->json(['message' => 'Pemesanan not found'], 404);
        }

        // Simpan atau perbarui data pembayaran ke tabel 'pembayaran'
        // Menggunakan updateOrCreate agar jika notifikasi datang berkali-kali, tidak membuat duplikat
        Pembayaran::updateOrCreate(
            ['order_id' => $data['order_id']], // Kondisi untuk mencari record yang sudah ada
            [
                'pembayaran_id' => Str::uuid(), // Generate UUID baru jika ini adalah record baru
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'transaction_id' => $data['transaction_id'],
                'payment_type' => $data['payment_type'],
                'transaction_status' => $data['transaction_status'],
                'fraud_status' => $data['fraud_status'] ?? null,
                'gross_amount' => (int)$data['gross_amount'],
                'va_numbers' => json_encode($data['va_numbers'] ?? []), // Simpan sebagai JSON string
                'status' => $data['transaction_status'] === 'settlement' ? 'paid' : 'pending', // Atur status pembayaran di tabel pembayaran
                'waktu_bayar' => $data['transaction_time'] ?? now(), // Waktu transaksi dari Midtrans
            ]
        );

        // Update status pemesanan berdasarkan transaction_status dari Midtrans
        if ($data['transaction_status'] === 'settlement') {
            // Jika pembayaran berhasil (settlement)
            $pemesanan->update([
                'status' => 'Lunas',
                'metode_pembayaran' => $data['payment_type'],
                'nomor_transaksi' => $data['transaction_id'],
                'waktu_pembayaran' => $data['transaction_time'] ?? now(),
            ]);
            Log::info('Pemesanan ' . $pemesanan->kode_booking . ' updated to Lunas.');
        } elseif (in_array($data['transaction_status'], ['expire', 'cancel'])) {
            // Jika pembayaran kedaluwarsa atau dibatalkan
            $pemesanan->update([
                'status' => 'Batal',
                'metode_pembayaran' => $data['payment_type'],
                'nomor_transaksi' => $data['transaction_id'],
                'waktu_pembayaran' => $data['transaction_time'] ?? now(),
            ]);
            Log::info('Pemesanan ' . $pemesanan->kode_booking . ' updated to Batal.');
        }
        // Anda bisa menambahkan kondisi lain untuk status 'pending', 'deny', dll.
        // Untuk 'pending', status pemesanan bisa tetap 'belum_lunas' atau 'pending_pembayaran'

        return response()->json(['message' => 'Notification processed successfully'], 200);
    }
}
