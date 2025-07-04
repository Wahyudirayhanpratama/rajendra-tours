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
        $payload = json_decode($request->getContent(), true);
        Log::info('Midtrans Callback:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = number_format((float)($payload['gross_amount'] ?? 0), 2, '.', '');
        $signatureKey = $payload['signature_key'] ?? null;

        $serverKey = config('midtrans.server_key');
        $computedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        Log::info('SIGNATURE DEBUG', [
            'expected' => $computedSignature,
            'from_request' => $signatureKey,
            'raw_string' => $orderId . $statusCode . $grossAmount . $serverKey,
        ]);

        if ($signatureKey !== $computedSignature) {
            Log::warning('Invalid Midtrans Signature.', $payload);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pemesanan = Pemesanan::where('kode_booking', $orderId)->first();
        if (!$pemesanan) {
            Log::error('Pemesanan tidak ditemukan untuk order_id: ' . $orderId);
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        Pembayaran::updateOrCreate(
            ['order_id' => $orderId],
            [
                'pembayaran_id' => Str::uuid(),
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'payment_type' => $payload['payment_type'] ?? null,
                'transaction_status' => $payload['transaction_status'] ?? null,
                'fraud_status' => $payload['fraud_status'] ?? null,
                'gross_amount' => (int) $payload['gross_amount'],
                'va_numbers' => json_encode($payload['va_numbers'] ?? []),
                'status' => ($payload['transaction_status'] ?? null) === 'settlement' ? 'paid' : 'pending',
                'waktu_bayar' => isset($payload['transaction_time']) ? Carbon::parse($payload['transaction_time']) : now(),
            ]
        );

        if (($payload['transaction_status'] ?? null) === 'settlement') {
            $pemesanan->update([
                'status' => 'Lunas',
                'metode_pembayaran' => $payload['payment_type'] ?? null,
                'nomor_transaksi' => $payload['transaction_id'] ?? null,
                'waktu_pembayaran' => Carbon::parse($payload['transaction_time']),
            ]);
        }

        return response(['message' => 'Notification handled']);
    }
}
