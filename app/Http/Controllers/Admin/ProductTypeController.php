<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    public function store(Request $request, Product $product)
    {
      $validate = Validator::make($request->json()->all(), [
        "types" => "required|array",
        'types.*.name' => 'required|string|max:255',
        'types.*.price' => 'required|numeric',
        'types.*.weight' => 'required|numeric',
        'types.*.stock' => 'required|integer',
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
      
      $data = [];
      
      foreach($request->json('types') as $request) {
        $data[] = ProductType::create([
          'name' => $request['name'],
          'price' => $request['price'],
          'weight' => $request['weight'],
          'stock' => $request['stock'],
          'product_id' => $product->id
        ]);
      }

      return response()->json([
        'success' => true,
        'code' => 201,
        'message' => 'Created',
        'data' => $data
      ], 200);
    }

    public function update(Request $request, Product $product, ProductType $type) 
    {
      if($type->product_id != $product->id) {
        return response()->json([
          'success' => false,
          'code' => 404,
          'message' => 'Not Found',
          'errors' => [
              'Product type not found'
          ]
        ], 404);
      }

      $validate = Validator::make($request->json()->all(), [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'weight' => 'required|numeric',
        'stock' => 'required|integer',
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

      $type->update([
        'name' => $request->json('name'),
        'price' => $request->json('price'),
        'weight' => $request->json('weight'),
        'stock' => $request->json('stock')
      ]);

      return response()->json([
        'success' => true,
        'code' => 200,
        'message' => 'Product type updated successfully',
        'data' => $type
      ], 200);
    }

    public function destroy(Product $product, ProductType $type)
    {
      if($type->product_id != $product->id) {
        return response()->json([
          'success' => false,
          'code' => 404,
          'message' => 'Not Found',
          'errors' => [
              'Product type not found'
          ]
        ], 404);
      }

      if(OrderProduct::where('product_type_id', $type->id)->count() > 0 ) {
        return response()->json([
          'success' => false,
          'code' => 400,
          'message' => 'Bad Request',
          'errors' => ['Product cannot be deleted because it is still in use']
        ], 400);
      }

      $type->delete();
      
      return response()->json([
        'success' => true,
        'code' => 200,
        'message' => 'Product type deleted successfully',
      ], 200);
    }
}
