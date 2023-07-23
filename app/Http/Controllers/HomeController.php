<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function product(Request $request) {
        $products = Product::search($request)
          ->with(
            [
              'images' => function ($query) {
                $query->first();
              },
              'types' => function ($query) {
                $query->first();
              },
            ]
          )
          ->select('id', 'name')
          ->paginate($request->input('per_page', 8));

        foreach ($products as $product) {
          $product->image = isset($product->images[0]->image) ? $product->images[0]->image : null;
          $product->price = isset($product->types[0]->price) ? $product->types[0]->price : null;

          unset($product->images);
          unset($product->types);
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => $products->items(),
            'pagination' => [
              'current_page' => $products->currentPage(),
              'last_page' => $products->lastPage(),
              'per_page' => $products->perPage(),
              'total' => $products->total(),
            ]
        ], 200);
    }

    public function showProduct(Product $product) {
        $product = $product->with([
            'images',
            'types'
        ])
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->where('products.id', $product->id)
        ->select('products.id', 'products.name','products.slug','products.description','categories.name as category_name')
        ->get();
              
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => $product
        ], 200);
    }

    public function category() {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => $categories
        ], 200);
    }
}
