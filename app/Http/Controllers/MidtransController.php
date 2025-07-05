<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Log;
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
        Log::info($request->all());

        // Log raw input dari php://input untuk debugging masalah server
        $rawInput = file_get_contents('php://input');
        Log::info('RAW INPUT FROM PHP://INPUT:', ['raw_data' => $rawInput]);

        // Inisialisasi konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false); // Sesuaikan dengan env Anda
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            // Gunakan Midtrans\Notification untuk membaca dan memverifikasi notifikasi
            // Pustaka ini akan membaca rawInput secara internal dan memvalidasi signature_key
            $notif = new Notification();

            Log::error('Test');
            Log::info('NOTIF DATA (from $notif):', ['notif' => json_encode($notif)]);


            // Ambil data dari objek notifikasi
            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $grossAmount = (int) $notif->gross_amount; // Pastikan ini integer
            $paymentType = $notif->payment_type;
            $transactionTime = $notif->transaction_time;
            $transactionId = $notif->transaction_id;
            $fraudStatus = $notif->fraud_status;

            // Pastikan properti va_numbers ada sebelum diakses
            $vaNumbersRaw = property_exists($notif, 'va_numbers') ? $notif->va_numbers : [];
            Log::info('VA_NUMBERS RAW DATA (from $notif->va_numbers):', ['va_numbers' => json_encode($vaNumbersRaw)]);

            // Ambil va_number tunggal jika ada (misal: untuk bank transfer)
            $vaNumber = null;

            if (!empty($vaNumbersRaw) && is_array($vaNumbersRaw)) {
                Log::info('Processing vaNumbersRaw array for VA extraction...');
                foreach ($vaNumbersRaw as $key => $vaItem) {
                    // Pastikan $vaItem adalah objek atau array yang bisa diakses
                    $vaItem = (object) $vaItem; // Cast to object for consistent property access

                    Log::info("Processing VA item at index {$key}:", ['va_item_data' => json_encode($vaItem)]);

                    // Coba ambil va_number umum (untuk bank transfer selain Mandiri e-channel)
                    if (property_exists($vaItem, 'va_number') && $vaItem->va_number !== null) {
                        $vaNumber = $vaItem->va_number;
                        Log::info('Extracted va_number (general):', ['va_number' => $vaNumber]);
                        break;
                    }
                    // Coba ambil bill_key dan biller_code untuk echannel (Mandiri Bill Payment)
                    elseif (property_exists($vaItem, 'bill_key') && property_exists($vaItem, 'biller_code') && $vaItem->bill_key !== null && $vaItem->biller_code !== null) {
                        $vaNumber = $vaItem->biller_code . '-' . $vaItem->bill_key; // Gabungkan biller_code dan bill_key
                        Log::info('Extracted bill_key/biller_code (echannel):', ['va_number' => $vaNumber]);
                        break;
                    }
                    // Tambahan: Coba ambil permata_va_number jika ada di dalam array va_numbers (untuk Permata VA)
                    elseif (property_exists($vaItem, 'permata_va_number') && $vaItem->permata_va_number !== null) {
                        $vaNumber = $vaItem->permata_va_number;
                        Log::info('Extracted permata_va_number (from VA array):', ['va_number' => $vaNumber]);
                        break;
                    }
                }
            }

            // Tambahan pengecekan untuk permata_va_number jika ada langsung di notif root (kasus khusus Permata VA)
            if ($vaNumber === null && property_exists($notif, 'permata_va_number') && $notif->permata_va_number !== null) {
                $vaNumber = $notif->permata_va_number;
                Log::info('Extracted permata_va_number (from root):', ['va_number' => $vaNumber]);
            }
            // Tambahan pengecekan untuk bill_key/biller_code langsung di notif root (jarang, tapi jaga-jaga)
            if ($vaNumber === null && property_exists($notif, 'bill_key') && property_exists($notif, 'biller_code') && $notif->bill_key !== null && $notif->biller_code !== null) {
                $vaNumber = $notif->biller_code . '-' . $notif->bill_key;
                Log::info('Extracted bill_key/biller_code (from root):', ['va_number' => $vaNumber]);
            }
            // Tambahan pengecekan untuk va_numbers (beberapa payment type mungkin menggunakan ini)
            if ($vaNumber === null && property_exists($notif, 'va_numbers') && $notif->va_numbers !== null) {
                $vaNumber = $notif->va_numbers;
                Log::info('Extracted va_numbers (from root):', ['va_number' => $vaNumber]);
            }

            // Log semua data yang berhasil diurai dari objek notifikasi
            Log::info('PARSED NOTIFICATION DATA:', [
                'transaction_status' => $transactionStatus,
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'payment_type' => $paymentType,
                'transaction_time' => $transactionTime,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'va_numbers_raw_logged' => json_encode($vaNumbersRaw),
                'va_number_single' => $vaNumber, // Log VA number tunggal
            ]);

            // Cari pemesanan berdasarkan kode_booking (yang sama dengan order_id Midtrans)
            $pemesanan = Pemesanan::where('kode_booking', $orderId)->first();

            if (!$pemesanan) {
                Log::error('Pemesanan not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Pemesanan not found'], 404);
            }

            // Simpan atau perbarui data pembayaran ke tabel 'pembayaran'
            // Menggunakan updateOrCreate agar jika notifikasi datang berkali-kali, tidak membuat duplikat
            Pembayaran::updateOrCreate(
                ['order_id' => $orderId], // Kondisi untuk mencari record yang sudah ada
                [
                    'pembayaran_id' => Str::uuid(), // Generate UUID baru jika ini adalah record baru
                    'pemesanan_id' => $pemesanan->pemesanan_id,
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus ?? null,
                    'gross_amount' => $grossAmount, // Menggunakan grossAmount dari notifikasi
                    'va_numbers' => json_encode($vaNumbersRaw), // Simpan sebagai JSON string
                    'status' => $transactionStatus === 'settlement' ? 'paid' : 'pending', // Atur status pembayaran di tabel pembayaran
                    'waktu_bayar' => $transactionTime ?? now(), // Waktu transaksi dari Midtrans
                ]
            );

            // Update status pemesanan berdasarkan transaction_status dari Midtrans
            if ($transactionStatus === 'settlement') {
                // Jika pembayaran berhasil (settlement)
                $updateData = [
                    'status' => 'Lunas',
                    'transaction_id' => $transactionId,
                    'transaction_time' => $transactionTime ?? now(),
                    'payment_type' => $paymentType,
                    'va_number' => $vaNumber, // Simpan VA number tunggal
                    'gross_amount' => $grossAmount, // Simpan gross_amount dari notifikasi
                ];
                // Log data yang akan di-update ke tabel pemesanan
                Log::info('UPDATING PEMESANAN WITH DATA:', $updateData);

                $pemesanan->update($updateData);
                Log::info('Pemesanan ' . $pemesanan->kode_booking . ' updated to Lunas.');
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                // Jika pembayaran kedaluwarsa, dibatalkan, atau ditolak
                $updateData = [
                    'status' => 'Batal', // Atau 'Gagal' untuk 'deny'
                    'transaction_id' => $transactionId,
                    'transaction_time' => $transactionTime ?? now(),
                    'payment_type' => $paymentType,
                    'va_number' => $vaNumber, // Simpan VA number tunggal
                    'gross_amount' => $grossAmount, // Simpan gross_amount dari notifikasi
                ];
                // Log data yang akan di-update ke tabel pemesanan
                Log::info('UPDATING PEMESANAN WITH DATA (CANCEL/EXPIRE):', $updateData);

                // Jika pembayaran kedaluwarsa, dibatalkan, atau ditolak
                $pemesanan->update($updateData);
                Log::info('Pemesanan ' . $pemesanan->kode_booking . ' updated to Batal.');
            }
            Log::info('UPDATE DATA FINAL:', $updateData);
            return response()->json(['message' => 'Notification processed successfully'], 200);

        } catch (\Exception $e) {
            // Tangani error jika ada masalah saat memproses notifikasi
            Log::error('Error processing Midtrans notification: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
