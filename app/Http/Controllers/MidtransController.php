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
        try {
            $request->merge(json_decode($request->getContent(), true) ?? []);
            Log::info('Midtrans Callback:', $request->all());

            $serverKey = config('midtrans.server_key');
            $raw = $request->order_id . $request->status_code . $request->gross_amount . $serverKey;
            $computedSignature = hash('sha512', $raw);

            Log::info('SIGNATURE DEBUG', [
                'expected' => $computedSignature,
                'from_request' => $request->signature_key,
                'raw_string' => $raw,
            ]);

            if ($request->signature_key !== $computedSignature) {
                Log::warning('Invalid Midtrans Signature.', $request->all());
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $pemesanan = Pemesanan::where('kode_booking', $request->order_id)->first();
            if (!$pemesanan) {
                Log::error('Pemesanan tidak ditemukan untuk order_id: ' . $request->order_id);
                return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
            }

            Pembayaran::updateOrCreate(
                ['order_id' => $request->order_id],
                [
                    'pembayaran_id' => Str::uuid(),
                    'pemesanan_id' => $pemesanan->pemesanan_id,
                    'transaction_id' => $request->transaction_id,
                    'payment_type' => $request->payment_type,
                    'transaction_status' => $request->transaction_status,
                    'fraud_status' => $request->fraud_status ?? null,
                    'gross_amount' => (int)$request->gross_amount,
                    'va_numbers' => json_encode($request->va_numbers ?? []),
                    'status' => $request->transaction_status === 'settlement' ? 'paid' : 'pending',
                    'waktu_bayar' => $request->transaction_time ? Carbon::parse($request->transaction_time) : now(),
                ]
            );

            if ($request->transaction_status === 'settlement') {
                $pemesanan->update([
                    'status' => 'Lunas',
                    'metode_pembayaran' => $request->payment_type,
                    'nomor_transaksi' => $request->transaction_id,
                    'waktu_pembayaran' => Carbon::parse($request->transaction_time),
                ]);
            }

            if (strtolower($request->signature_key) !== strtolower($computedSignature)) {
                Log::warning('Invalid Midtrans Signature.', [
                    'from_request' => $request->signature_key,
                    'expected' => $computedSignature,
                    'raw_string' => $request->order_id . $request->status_code . $request->gross_amount . $serverKey,
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        } catch (\Exception $e) {
            Log::error('MIDTRANS ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
