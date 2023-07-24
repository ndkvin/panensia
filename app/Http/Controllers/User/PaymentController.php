<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\PaymentMethod;
use App\Models\ProductType;
use Illuminate\Http\Request;
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
    public function index()
    {
        //
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
