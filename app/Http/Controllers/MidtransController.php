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

            $notif = new Notification(); // Ini akan memicu validasi signature_key (menggunakan rawInput secara internal)

            // --- Mulai Penguraian Data Notifikasi Secara Manual dari rawInput ---
            // Langkah 1: Decode lapisan JSON terluar
            $outerData = json_decode($rawInput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode outer JSON input: ' . json_last_error_msg(), ['raw_input' => $rawInput]);
                return response()->json(['message' => 'Invalid outer JSON received'], 400);
            }

            // Langkah 2: Ambil string JSON dari key 'raw_data' dan decode lagi
            $notificationDataString = $outerData['raw_data'] ?? null;

            if ($notificationDataString === null) {
                Log::error('Key "raw_data" not found in outer JSON input.', ['outer_data' => $outerData]);
                return response()->json(['message' => 'Missing "raw_data" key in notification'], 400);
            }

            $notificationData = json_decode($notificationDataString, true); // Ini adalah payload Midtrans yang sebenarnya

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode inner JSON (raw_data content): ' . json_last_error_msg(), ['raw_data_string' => $notificationDataString]);
                return response()->json(['message' => 'Invalid inner JSON received'], 400);
            }

            // Ambil data dari array $notificationData yang sudah di-decode dengan benar
            $transactionStatus = $notificationData['transaction_status'] ?? null;
            $orderId = $notificationData['order_id'] ?? null;
            $grossAmount = (int) ($notificationData['gross_amount'] ?? 0);
            $paymentType = $notificationData['payment_type'] ?? null;
            $transactionTime = $notificationData['transaction_time'] ?? null;
            $transactionId = $notificationData['transaction_id'] ?? null;
            $fraudStatus = $notificationData['fraud_status'] ?? null;

            // Pastikan properti va_numbers ada sebelum diakses
            $vaNumbersRaw = $notificationData['va_numbers'] ?? [];
            Log::info('VA_NUMBERS RAW DATA (from manual decode - after double decode):', ['va_numbers' => json_encode($vaNumbersRaw)]);

            // Ambil va_number tunggal jika ada (misal: untuk bank transfer)
            $vaNumber = null;

            // Prioritas 1: Coba ekstrak dari array va_numbers jika tidak kosong
            if (!empty($vaNumbersRaw) && is_array($vaNumbersRaw)) {
                Log::info('Processing vaNumbersRaw array for VA extraction...');
                foreach ($vaNumbersRaw as $key => $vaItem) {
                    // Pastikan $vaItem adalah array sebelum di-cast ke objek
                    if (is_array($vaItem)) {
                        $vaItem = (object) $vaItem; // Cast to object for consistent property access
                    } else {
                        // Jika bukan array, mungkin sudah objek, atau ada masalah. Log dan lewati.
                        Log::warning("VA item at index {$key} is not an array, skipping. Data: " . json_encode($vaItem));
                        continue;
                    }

                    Log::info("Processing VA item at index {$key}:", ['va_item_data' => json_encode($vaItem)]);

                    if (property_exists($vaItem, 'va_number') && $vaItem->va_number !== null) {
                        $vaNumber = $vaItem->va_number;
                        Log::info('Extracted va_number (general from array):', ['va_number' => $vaNumber]);
                        break;
                    } elseif (property_exists($vaItem, 'bill_key') && property_exists($vaItem, 'biller_code') && $vaItem->bill_key !== null && $vaItem->biller_code !== null) {
                        $vaNumber = $vaItem->biller_code . '-' . $vaItem->bill_key;
                        Log::info('Extracted bill_key/biller_code (echannel from array):', ['va_number' => $vaNumber]);
                        break;
                    } elseif (property_exists($vaItem, 'permata_va_number') && $vaItem->permata_va_number !== null) {
                        $vaNumber = $vaItem->permata_va_number;
                        Log::info('Extracted permata_va_number (from VA array):', ['va_number' => $vaNumber]);
                        break;
                    }
                }
            }

            // Prioritas 2: Jika belum ditemukan, coba ekstrak langsung dari root notificationData array
            // Gunakan isset() untuk mengecek keberadaan key di array asosiatif
            if ($vaNumber === null) {
                Log::info('VA not found in vaNumbersRaw array, checking root properties of decoded data...');
                if (isset($notificationData['permata_va_number']) && $notificationData['permata_va_number'] !== null) {
                    $vaNumber = $notificationData['permata_va_number'];
                    Log::info('Extracted permata_va_number (from root decoded data):', ['va_number' => $vaNumber]);
                } elseif (isset($notificationData['bill_key']) && isset($notificationData['biller_code']) && $notificationData['bill_key'] !== null && $notificationData['biller_code'] !== null) {
                    $vaNumber = $notificationData['biller_code'] . '-' . $notificationData['bill_key'];
                    Log::info('Extracted bill_key/biller_code (from root decoded data):', ['va_number' => $vaNumber]);
                } elseif (isset($notificationData['virtual_account_number']) && $notificationData['virtual_account_number'] !== null) {
                    $vaNumber = $notificationData['virtual_account_number'];
                    Log::info('Extracted virtual_account_number (from root decoded data):', ['va_number' => $vaNumber]);
                }
                // Tambahan pengecekan untuk VA spesifik bank jika ada di root
                elseif (isset($notificationData['bca_va_number']) && $notificationData['bca_va_number'] !== null) {
                    $vaNumber = $notificationData['bca_va_number'];
                    Log::info('Extracted bca_va_number (from root decoded data):', ['va_number' => $vaNumber]);
                }
                elseif (isset($notificationData['bni_va_number']) && $notificationData['bni_va_number'] !== null) {
                    $vaNumber = $notificationData['bni_va_number'];
                    Log::info('Extracted bni_va_number (from root decoded data):', ['va_number' => $vaNumber]);
                }
                elseif (isset($notificationData['bri_va_number']) && $notificationData['bri_va_number'] !== null) {
                    $vaNumber = $notificationData['bri_va_number'];
                    Log::info('Extracted bri_va_number (from root decoded data):', ['va_number' => $vaNumber]);
                }
                // Tambahkan pengecekan untuk bank lain jika diperlukan (contoh: cimb_va_number, danamon_va_number, dll.)
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
            return response()->json(['message' => 'Notification processed successfully'], 200);

        } catch (\Exception $e) {
            // Tangani error jika ada masalah saat memproses notifikasi
            Log::error('Error processing Midtrans notification: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
