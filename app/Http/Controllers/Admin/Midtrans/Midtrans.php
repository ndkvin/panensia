<?php

namespace App\Http\Controllers\Admin\Midtrans;

use App\Models\Config as ModelsConfig;

class Midtrans
{
    public $config;

    public static function createPayment($invoice, $total)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $enabled_payment = $total < ModelsConfig::where('key', 'minimum_va')->first()->value ? ["gopay"] : ["permata_va", "bca_va", "bni_va", "other_va", "gopay",];

        $params = array(
            'transaction_details' => array(
                'order_id' => $invoice,
                'gross_amount' => $total,
            ),
            'customer_details' => array(
                'first_name' => 'budi',
                'last_name' => 'pratama',
                'email' => 'budi.pra@example.com',
                'phone' => '08111222333',
            ),
            'enabled_payments' => $enabled_payment,
        );

        return \Midtrans\Snap::getSnapToken($params);
    }
}
