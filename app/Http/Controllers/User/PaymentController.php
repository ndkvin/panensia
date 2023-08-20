<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function available(Request $request)
    {
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Manual Transfer'
                ],
                [
                    'id' => 2,
                    'name' => 'Automatic Verification'
                ]
            ]
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Order $order)
    {
        if($order->user_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden',
                'errors' => [
                    'You are not allowed to access this resource'
                ]
            ], 403);
        }

        if($order->payment_method == "MIDTRANS") {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'Please paid in payment gateway'
                ]
            ], 400);
        }

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'OK',
          'data' => [
            'payment_status' => $order->status,
          ]
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
    public function store(Request $request, Order $order)
    {
        if($order->user_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden',
                'errors' => [
                    'You are not allowed to access this resource'
                ]
            ], 403);
        }

        if($order->payment_method == "MIDTRANS") {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'No need to crate payment for this order, the payment wil automaticly confirmed after you paid the order'
                ]
            ], 400);
        }
        
        if($order->status != 'UNPAID') {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'You are not allowed to access this resource'
                ]
            ], 400);
        }

        $validate = Validator::make($request->json()->all(), [
            'name' => 'required|string',
            'bank' => 'required|string',
            'rekening' => 'required|string',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $errorMessage = [];

            foreach ($errors->all() as $message) {
              $errorMessage[] = $message;
            }
          
            return response()->json([
              'success' => false,
              'code' => 422,
              'message' => 'Unprocessable Entity',
              'errors' => $errorMessage
            ], 422);
        }

        DB::beginTransaction();

        try {
          $payment = Payment::create([
              'order_id' => $order->id,
              'name' => $request->json('name'),
              'bank' => $request->json('bank'),
              'rekening' => $request->json('rekening'),
          ]);

          $order->update([
              'status' => 'PENDING'
          ]);

          DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Internal Server Error',
            ], 500);
        }

        return response()->json([
          'success' => true,
          'code' => 201,
          'message' => 'Created, please send the payment proof',
          'data' => $payment
        ], 201);
    }

    public function storeImage(Request $request, Order $order)
    { 
        if($order->user_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden',
                'errors' => [
                    'You are not allowed to access this resource'
                ]
            ], 403);
        }
        $payment = Payment::where('order_id', $order->id)->first();
        if(!$payment->image == null) {
          return response()->json([
            'success' => false,
            'code' => 400,
            'message' => 'Bad Request',
            'errors' => [
              'already post payment.'
            ]
          ], 400);
        } 

        $validate = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $errorMessage = [];

            foreach ($errors->all() as $message) {
              $errorMessage[] = $message;
            }
          
            return response()->json([
              'success' => false,
              'code' => 422,
              'message' => 'Unprocessable Entity',
              'errors' => $errorMessage
            ], 422);
        }

        if($order->status != 'PENDING') {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'You are not allowed to access this resource'
                ]
            ], 400);
        }

        if($payment->image != null) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'You are not allowed to access this resource'
                ]
            ], 400);
        }

        $payment->update([
          'image' => $request->file('image')->store('images/payment', 'public')
        ]);

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'OK',
          'data' => $payment
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
