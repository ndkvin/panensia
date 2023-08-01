<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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
        $order = $order->with([
            'orderProducts' => function ($query) {
                $query->with([
                    'productType' => function ($query) {
                        $query->select('id',  'name');
                    },
                ]);
            },
        ])
        ->join('users', 'orders.user_id', '=', 'users.id')
        ->select('orders.id', 'invoice', 'status', 'total', 'orders.created_at', 'payment_method', 'users.name as user_name')
        ->where('orders.id', $order->id)
        ->first();

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
