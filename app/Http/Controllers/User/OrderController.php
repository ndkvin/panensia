<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Config;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
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
        $orders = Order::where('user_id', auth()->user()->id)->paginate($request->input('per_page', 10));
        
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
        $validate = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_type_id' => 'required|numeric|exists:product_types,id',
            'items.*.quantity' => 'required|numeric',
            'address.address' => 'required|string',
            'address.street' => 'required|string',
            'address.city' => 'required|string',
            'address.province' => 'required|string',
            'address.postal_code' => 'required|numeric',
            'address.phone' => 'required|string|min:10|max:13',
            'payment' => 'required|numeric|in:1,2,3,4,5,6'
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
            $order = Order::create([
                'user_id' => auth()->user()->id,
                'invoice' => 'INV-' . strtoupper(Str::random(6)),
                'payment_ref' => '123123',
                'total' => 0,
                'payment_method_id' => $request->payment,
                'address' => $request->address['address'],
                'street' => $request->address['street'],
                'city' => $request->address['city'],
                'province' => $request->address['province'],
                'postal_code' => $request->address['postal_code'],
                'phone' => $request->address['phone'],
                'status' => 'UNPAID',
            ]);
            
            $total = 0;

            foreach($request->items as $item) {
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
                ]);
                $total += $productType->price * $item['quantity'];

                Cart::where('user_id', auth()->user()->id)
                    ->where('product_type_id', $item['product_type_id'])
                    ->delete();

                $productType->stock -= $item['quantity'];
                $productType->save();
            }

            if($total < Config::where('key', 'minimum_va')->first()->value && PaymentMethod::find($request->payment)->minimum) {
                DB::rollBack();
                return response([
                  'success' => false,
                  'code' => 422,
                  'message' => 'Unprocessable Entity',
                  'errors' => ['Minimum payment to use va is ' . Config::where('key', 'minimum_va')->first()->value]
                ]);
            }

            $order->total = $total;
            $order->save();

            DB::commit();
            
            return response()->json([
              'success' => true,
              'code' => 201,
              'message' => 'Created',
              'data' => $order
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
          'orderProducts' => function($query) {
            $query->join('product_types', 'order_products.product_type_id', '=', 'product_types.id')
                  ->join('products', 'product_types.product_id', '=', 'products.id')
                  ->select('order_products.*','product_types.price', 'products.name');
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
