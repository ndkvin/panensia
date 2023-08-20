<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\Midtrans\Midtrans;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->paginate($request->input('per_page', 10));
        
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
        $validate = Validator::make($request->json()->all(), [
            'items' => 'required|array',
            'items.*.product_type_id' => 'required|numeric|exists:product_types,id',
            'items.*.quantity' => 'required|numeric',
            'address.address' => 'required|string',
            'address.street' => 'required|string',
            'address.city' => 'required|string',
            'address.province' => 'required|string',
            'address.postal_code' => 'required|numeric',
            'address.phone' => 'required|string|min:10|max:13',
            'address.name' => 'required|string',
            'payment' => 'required|numeric|in:1,2',
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

        $payment_method = $request->json('payment') == 1 ? "MANUAL" : "MIDTRANS";
        try {
            $order = Order::create([
                'user_id' => auth()->user()->id,
                'invoice' => 'INV-' . strtoupper(Str::random(6)),
                'payment_ref' => '0',
                'total' => 0,
                'payment_method' => $payment_method,
                'status' => 'UNPAID',
            ]);

            $address = $request->json('address');

            $order->address()->create([
                'name' => $address['name'],
                'address' => $address['address'],
                'street' => $address['street'],
                'city' => $address['city'],
                'province' => $address['province'],
                'postal_code' => $address['postal_code'],
                'phone' => $address['phone'],
            ]);

            $weight = 0;
            $total = 0;

            foreach($request->json('items') as $item) {
                $productType = ProductType::find($item['product_type_id']);

                if ($productType->stock < $item['quantity']) {
                    $product = ProductType::with('product')->find($item['product_type_id']);
                    $name = $product->product->name;
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'code' => 422,
                        'message' => 'Unprocessable Entity',
                        'errors' => ["Stock $name not enough"]
                    ], 422);
                }

                OrderProduct::create([
                  'order_id' => $order->id,
                  'product_type_id' => $item['product_type_id'],
                  'quantity' => $item['quantity'],
                  'item_price' => $productType->price,
                ]);

                $total += $productType->price * $item['quantity'];
                $weight += $productType->weight * $item['quantity'];

                Cart::where('user_id', auth()->user()->id)
                    ->where('product_type_id', $item['product_type_id'])
                    ->delete();

                $productType->stock -= $item['quantity'];
                $productType->save();
            }

            if($request->json('payment') == 2) {
                $response = Midtrans::createPayment($order->invoice, $total);

                $order->payment_ref = $response->token;
                $order->total = $total;

                $order->save();

                $order->shipment()->create([
                  'weight' => $weight,
                ]);

                DB::commit();

                $order->weight = $weight;

                return response()->json([
                  'success' => true,
                  'code' => 201,
                  'message' => 'Created',
                  'data' => [
                    'order' => $order,
                    'payment' => $response
                  ],
                ]);
            }

            $order->total = $total;
            $order->save();

            $order->shipment()->create([
              'weight' => $weight,
            ]);

            DB::commit();

            $order->weight = $weight;

            return response()->json([
              'success' => true,
              'code' => 201,
              'message' => 'Created',
              'data' => [
                'order' => $order,
              ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
              'success' => false,
              'code' => 500,
              'message' => 'Internal Server Error',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {   
        if($order->user_id != auth()->user()->id) {
          return response()->json([
            'success' => false,
            'code' => 403,
            'message' => 'Forbidden',
          ], 403);
        }
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
