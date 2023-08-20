<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $address = UserAddress::where('user_id', auth()->user()->id)->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address list',
            'data' => $address->items(),
            'pagination' => [
                'current_page' => $address->currentPage(),
                'last_page' => $address->lastPage(),
                'per_page' => $address->perPage(),
                'total' => $address->total(),
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
    public function store(Request $request)
    {
        $validate = Validator::make($request->json()->all(), [
            'address' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|numeric',
            'phone' => 'required|string|min:10|max:13',
            'name' => 'required|string'
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

        $address = UserAddress::create([
            'user_id' => auth()->user()->id,
            'address' => $request->address,
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'code' => 201,
            'message' => 'Address created',
            'data' => $address
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserAddress $address)
    {
        if($address->user_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address detail',
            'data' => $address
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
    public function update(Request $request, UserAddress $address)
    {
        if($address->user_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        $validate = Validator::make($request->json()->all(), [
            'address' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|numeric',
            'phone' => 'required|string|min:10|max:13',
            'name' => 'required|string'
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

        $address->update([
            'address' => $request->address,
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address updated',
            'data' => $address
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserAddress $address)
    {
        if($address->user_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address deleted'
        ]);
    }
}
