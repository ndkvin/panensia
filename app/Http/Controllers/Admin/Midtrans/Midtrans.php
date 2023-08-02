<?php

namespace App\Http\Controllers\Admin\Midtrans;

use App\Models\Config as ModelsConfig;
use App\Models\Order;
use \Midtrans\Config;
use \Midtrans\Snap;
use \Midtrans\Notification;

class Midtrans
{
    public $config;

    public static function createPayment($invoice, $total)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $enabled_payment = $total < ModelsConfig::where('key', 'minimum_va')->first()->value ? ["shopeepay"] : ["permata_va", "bca_va", "bni_va", "other_va", "shopeepay"];

        $user = auth()->user();
        $params = array(
            'transaction_details' => array(
                'order_id' => $invoice,
                'gross_amount' => $total,
            ),
            'customer_details' => array(
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone' => $user->phone,
            ),
            'enabled_payments' => $enabled_payment,
        );

        return Snap::createTransaction($params);
    }

    public static function callback()
    {
        Config::$isProduction = false;
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;

        if ($transaction == 'settlement') {
            Order::where('invoice', $order_id)
                ->update([
                    'status' => 'PAID',
                    'payment_method' => $type,
                ]);
        } else if ($transaction == 'pending') {
            Order::where('invoice', $order_id)
                ->update([
                    'status' => 'PENDING',
                    'payment_method' => $type,
                ]);
        } else if ($transaction == 'deny') {
            Order::where('invoice', $order_id)
                ->update([
                    'status' => 'DENY',
                    'payment_method' => $type,
                ]);
        } else if ($transaction == 'expire') {
            Order::where('invoice', $order_id)
                ->update([
                    'status' => 'EXPIRED',
                    'payment_method' => $type,
                ]);
            
        } else if ($transaction == 'cancel') {
            Order::where('invoice', $order_id)
                ->update([
                    'status' => 'CANCEL',
                    'payment_method' => $type,
                ]);
        }
    }
}
