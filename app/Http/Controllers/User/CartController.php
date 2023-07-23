<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::where('user_id', auth()->user()->id)->get();

        foreach($carts as $cart) {
            $productType = ProductType::find($cart->product_type_id);
            $cart->stock_available = $productType->stock;
            $cart->is_available = $productType->stock >= $cart->quantity ? true : false;
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Cart list',
            'data' => $carts,
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
        $validate = Validator::make($request->all(), [
          'product_type_id' => 'required|exists:product_types,id',
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

        $productType = ProductType::find($request->product_type_id);

        $cart = Cart::where('user_id', auth()->user()->id)->where('product_type_id', $request->product_type_id)->first();


        // if stock is 0
        if($productType->stock == 0) {
          return response()->json([
            'success' => false,
            'code' => 422,
            'message' => 'Unprocessable Entity',
            'errors' => ['Product out of stock']
          ], 422);
        }

        // if cart is empty
        if(!$cart) {
          $cart = Cart::create([
            'user_id' => auth()->user()->id,
            'product_type_id' => $request->product_type_id,
            'quantity' => 1,
          ]);
  
          return response()->json([
            'success' => true,
            'code' => 201,
            'message' => 'Cart created',
            'data' => $cart
          ], 201);
        }

        if($cart->quantity + 1 > $productType->stock) {
          return response()->json([
            'success' => false,
            'code' => 422,
            'message' => 'Unprocessable Entity',
            'errors' => ['Product out of stock']
          ], 422);
        }

        $cart->quantity = $cart->quantity + 1;
        $cart->save();

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Cart updated',
          'data' => $cart
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

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        if($cart->user_id != auth()->user()->id) {
          return response()->json([
            'success' => false,
            'code' => 403,
            'message' => 'Forbidden',
          ], 403);
        }

        $validate = Validator::make($request->all(), [
          'increment' => 'required|boolean',
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

        if(!$request->increment) {
          $cart->quantity = $cart->quantity - 1;

          if($cart->quantity == 0) {
            $cart->delete();
            return response()->json([
              'success' => true,
              'code' => 200,
              'message' => 'Cart deleted',
            ], 200);
          }

          $cart->save();
          return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Cart updated',
            'data' => $cart
          ], 200);
        } 

        $productType = ProductType::find($cart->product_type_id);

        if($cart->quantity + 1 > $productType->stock) {
          return response()->json([
            'success' => false,
            'code' => 422,
            'message' => 'Unprocessable Entity',
            'errors' => ['Product out of stock']
          ], 422);
        }

        $cart->quantity = $cart->quantity + 1;
        $cart->save();
        
        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Cart updated',
          'data' => $cart
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        if($cart->user_id != auth()->user()->id) {
          return response()->json([
            'success' => false,
            'code' => 403,
            'message' => 'Forbidden',
          ], 403);
        }

        $cart->delete();

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Cart deleted',
        ], 200);
    }
}
