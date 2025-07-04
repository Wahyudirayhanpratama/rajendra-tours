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
        $data = $request->all();
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        Log::info('Midtrans Callback:', $data);

        $serverKey = config('midtrans.server_key');

        $raw = $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey;
        $computedSignature = hash('sha512', $raw);

        Log::info('SIGNATURE DEBUG', [
            'expected' => $computedSignature,
            'from_request' => $data['signature_key'] ?? null,
            'raw_string' => $raw
        ]);

        if (!isset($data['signature_key']) || strtolower($data['signature_key']) !== strtolower($computedSignature)) {
            Log::warning('Invalid Midtrans Signature.', $data);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pemesanan = Pemesanan::where('kode_booking', $data['order_id'])->first();
        if (!$pemesanan) {
            Log::error('Pemesanan tidak ditemukan untuk order_id: ' . $data['order_id']);
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        Pembayaran::updateOrCreate(
            ['order_id' => $data['order_id']],
            [
                'pembayaran_id' => Str::uuid(),
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'transaction_id' => $data['transaction_id'],
                'payment_type' => $data['payment_type'],
                'transaction_status' => $data['transaction_status'],
                'fraud_status' => $data['fraud_status'] ?? null,
                'gross_amount' => (int)$data['gross_amount'],
                'va_numbers' => json_encode($data['va_numbers'] ?? []),
                'status' => $data['transaction_status'] === 'settlement' ? 'paid' : 'pending',
                'waktu_bayar' => $data['transaction_time'] ?? now(),
            ]
        );

        if ($data['transaction_status'] === 'settlement') {
            $pemesanan->update([
                'status' => 'Lunas',
                'metode_pembayaran' => $data['payment_type'],
                'nomor_transaksi' => $data['transaction_id'],
                'waktu_pembayaran' => $data['transaction_time'],
            ]);
        }

        return response(['message' => 'Notification handled']);
    }
}
