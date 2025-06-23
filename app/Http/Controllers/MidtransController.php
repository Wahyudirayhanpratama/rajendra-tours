<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Services\MidtransService;
use Carbon\Carbon;
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
        $serverKey = config('midtrans.server_key');
        $computedSignature = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );

        if ($request->signature_key !== $computedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pemesanan = Pemesanan::where('kode_booking', $request->order_id)->first();
        if (!$pemesanan) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        if ($request->transaction_status === 'settlement') {
            $pemesanan->update([
                'status' => 'Lunas',
                'metode_pembayaran' => $request->payment_type,
                'nomor_transaksi' => $request->transaction_id,
                'waktu_pembayaran' => Carbon::parse($request->transaction_time),
            ]);
        }

        return response(['message' => 'Notification handled']);
    }
}
