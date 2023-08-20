<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::search($request)
          ->join('users', 'orders.user_id', '=', 'users.id')
          ->select('orders.id', 'orders.invoice', 'orders.status', 'orders.total', 'orders.created_at', 'users.name as user_name')
          ->orderBy('orders.id', 'desc')
          ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Order list',
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
    public function show(Order $order)
    {
        $order = Order::with([
          'address',
          'shipment',
          'orderProducts' => function($query) {
            $query->join('product_types', 'order_products.product_type_id', '=', 'product_types.id')
                  ->join('products', 'product_types.product_id', '=', 'products.id')
                  ->Join('product_images', function ($join) {
                    $join->on('product_images.product_id', '=', 'products.id')
                      ->whereRaw('product_images.id = (SELECT id FROM product_images WHERE product_id = products.id ORDER BY created_at ASC LIMIT 1)');
                  })
                  ->select('order_products.*', 'products.name', 'product_images.image');
          },
        ])->where('id', $order->id)->first();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Order detail',
            'data' => $order,
        ]);
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
    public function update(Request $request, Order $order)
    {
        if($order->status != 'PAID') {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Cannot update shipment, because order status is not PAID',
            ], 400);
        }

        $validate = Validator::make($request->json()->all(), [
            'courier' => 'required|string',
            'service' => 'required|string',
            'shipping_cost' => 'required|integer',
            'resi' => 'required|string',
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

        $shipment = $order->shipment()->first();
        
        $shipment->update([
            'courier' => $request->json('courier'),
            'service' => $request->json('service'),
            'shipping_cost' => $request->json('shipping_cost'),
            'resi' => $request->json('resi'),
        ]);

        $order->update([
            'status' => 'SHIPPED',
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Order shipment updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
