<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $orders = Order::search($request)
        ->join('users', 'orders.user_id', '=', 'users.id')
        ->select('orders.id', 'orders.invoice', 'orders.status', 'orders.total', 'orders.created_at', 'users.name as user_name')
        ->where([
          ['orders.payment_method', '=', "MANUAL"],
          ['orders.status', '=', 'pending'],
        ])
        ->paginate($request->input('per_page', 10));

      return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Payment order list',
          'data' => $orders->items(),
          'pagination' => [
              'current_page' => $orders->currentPage(),
              'last_page' => $orders->lastPage(),
              'per_page' => $orders->perPage(),
              'total' => $orders->total(),
          ],
      ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $payment)
    {
        if($payment->payment_method != "MANUAL") {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'Please paid in payment gateway'
                ]
            ], 400);
        }
        
        if($payment->status != 'PENDING') {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    "order $payment->status"
                ]
            ], 400);
        }

        $payment = $payment->leftJoin('payments' , 'orders.id', '=', 'payments.order_id')
          ->select('orders.id', 'orders.invoice', 'orders.status', 'orders.status', 'payments.created_at as payment_date', 'payments.rekening', 'payments.name', 'payments.image')
          ->where([
              ['orders.id', $payment->id]
            ])
          ->get();
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => $payment,
            
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $payment)
    {
        if($payment->payment_method_id != 1) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'Please paid in payment gateway'
                ]
            ], 400);
        }
        
        if($payment->status != 'PENDING') {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    "order $payment->status"
                ]
            ], 400);
        }

        $payment->update([
          'status' => 'PAID',
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Payment status has been updated to PAID',
            'data' => [
                'payment_status' => $payment->status,
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
