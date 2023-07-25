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
        $validate = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_type_id' => 'required|numeric|exists:product_types,id',
            'items.*.quantity' => 'required|numeric',
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
        $total = 0;
        foreach($request->items as $item) {
            $productType = ProductType::find($item['product_type_id']);

            if($productType->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'code' => 422,
                    'message' => 'Unprocessable Entity',
                    'errors' => [
                        'Product type with id ' . $productType->id . ' is out of stock'
                    ]
                ], 422);
            }
            $total += $productType->price * $item['quantity'];
        } 

        if($total > Config::where('key', 'minimum_va')->first()->value) {
          return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [
              'total' => $total,
              'payment' => PaymentMethod::select('id', 'name')->get(),
            ]
          ], 200);
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => [
                'total' => $total,
                'payment' => PaymentMethod::where('minimum', '=', false)->select('id', 'name')->get(),
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

        if($order->payment_method_id != 1) {
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

        if($order->payment_method_id != 1) {
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

        $validate = Validator::make($request->all(), [
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
              'name' => $request->name,
              'bank' => $request->bank,
              'rekening' => $request->rekening,
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
