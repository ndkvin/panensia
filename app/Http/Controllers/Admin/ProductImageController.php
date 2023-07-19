<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    private function storeImage($images, string $id) {
      $paths = [];
      foreach ($images as $image) {
          $imageName = $image->store('images/product', 'public');
          $paths[] = $imageName;
      }
      $productImage = [];
      foreach($paths as $path) {
        $data = ProductImage::create([
          'product_id' => $id,
          'image' => $path
        ]);

        $productImage[] = $data;
      } 

      return $productImage;
    }

    public function store(Request $request, Product $product)
    {
      $validate = Validator::make($request->all(), [
        'image' => 'required|array',
        'image*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

      $image = $request->file('image');
      $data = $this->storeImage($image, $product->id);

      return response()->json([
        'success' => true,
        'code' => 201,
        'message' => 'Product image uploaded successfully',
        'data' => $data
      ], 201);
    }

    public function destroy(Product $product, ProductImage $image) 
    {
        if($image->product_id != $product->id) {
          return response()->json([
            'success' => false,
            'code' => 404,
            'message' => 'Not Found',
            'errors' => [
                'Product image not found'
            ]
          ], 404);
        }


        Storage::delete('public/' . $image->image);
        $image->delete();

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Product image deleted successfully',
        ], 200);
    }
}
