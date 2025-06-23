<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // Sandbox
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken($orderId, $grossAmount, $customerName)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customerName,
            ]
        ];

        return Snap::getSnapToken($params);
    }

    public function createSnapTransaction($params)
    {
        return Snap::getSnapToken($params);
    }
}
