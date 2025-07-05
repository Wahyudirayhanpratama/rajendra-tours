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
        Log::info('MIDTRANS NOTIFICATION RECEIVED');

        // Inisialisasi konfigurasi Midtrans (pastikan sesuai dengan env Anda)
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false); // Sesuaikan dengan env Anda
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            // Gunakan Midtrans\Notification untuk membaca dan memverifikasi notifikasi
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $grossAmount = $notif->gross_amount;
            $paymentType = $notif->payment_type;
            $transactionTime = $notif->transaction_time;
            $transactionId = $notif->transaction_id;
            $fraudStatus = $notif->fraud_status;
            $vaNumbers = property_exists($notif, 'va_numbers') ? $notif->va_numbers : []; // Handle jika tidak ada VA numbers

            // Log semua data yang diterima dari objek notifikasi untuk debugging
            Log::info('PARSED NOTIFICATION DATA:', [
                'transaction_status' => $transactionStatus,
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'payment_type' => $paymentType,
                'transaction_time' => $transactionTime,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'va_numbers' => $vaNumbers,
            ]);

            // Cari pemesanan berdasarkan kode_booking (yang sama dengan order_id Midtrans)
            $pemesanan = Pemesanan::where('kode_booking', $orderId)->first();

            if (!$pemesanan) {
                Log::error('Pemesanan not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Pemesanan not found'], 404);
            }

            // Simpan atau perbarui data pembayaran ke tabel 'pembayaran'
            Pembayaran::updateOrCreate(
                ['order_id' => $orderId], // Kondisi untuk mencari record yang sudah ada
                [
                    'pembayaran_id' => Str::uuid(), // Generate UUID baru jika ini adalah record baru
                    'pemesanan_id' => $pemesanan->pemesanan_id,
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus ?? null,
                    'gross_amount' => (int)$grossAmount,
                    'va_numbers' => json_encode($vaNumbers), // Simpan sebagai JSON string
                    'status' => $transactionStatus === 'settlement' ? 'paid' : 'pending', // Atur status pembayaran di tabel pembayaran
                    'waktu_bayar' => $transactionTime ?? now(), // Waktu transaksi dari Midtrans
                ]
            );

            // Update status pemesanan berdasarkan transaction_status dari Midtrans
            if ($transactionStatus === 'settlement') {
                // Jika pembayaran berhasil (settlement)
                $pemesanan->update([
                    'status' => 'Lunas',
                    'metode_pembayaran' => $paymentType,
                    'nomor_transaksi' => $transactionId,
                    'waktu_pembayaran' => $transactionTime ?? now(),
                ]);
                Log::info('Pemesanan ' . $pemesanan->kode_booking . ' updated to Lunas.');
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                // Jika pembayaran kedaluwarsa, dibatalkan, atau ditolak
                $pemesanan->update([
                    'status' => 'Batal', // Atau 'Gagal' untuk 'deny'
                    'metode_pembayaran' => $paymentType,
                    'nomor_transaksi' => $transactionId,
                    'waktu_pembayaran' => $transactionTime ?? now(),
                ]);
                Log::info('Pemesanan ' . $pemesanan->kode_booking . ' updated to Batal.');
            }
            // Anda bisa menambahkan kondisi lain untuk status 'pending', 'challenge', dll.
            // Untuk 'pending', status pemesanan bisa tetap 'belum_lunas' atau 'pending_pembayaran'

            return response()->json(['message' => 'Notification processed successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
