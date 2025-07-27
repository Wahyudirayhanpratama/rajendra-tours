<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MidtransRefundController extends Controller
{
    public function refund(Request $request)
    {
        $orderId = $request->order_id; // ID pesanan yang ingin direfund
        $amount = $request->amount;    // Jumlah refund

        $serverKey = config('midtrans.server_key');
        $auth = base64_encode($serverKey . ':');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
            'Content-Type'  => 'application/json'
        ])->post("https://api.sandbox.midtrans.com/v2/{$orderId}/refund", [
            'refund_key'   => 'refund-demo-' . time(),
            'amount'       => $amount,
            'reason'       => 'Customer request'
        ]);

        return $response->json();
    }
}
