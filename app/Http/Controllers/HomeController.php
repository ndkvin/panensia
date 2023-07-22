<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request) {
        $products = Product::name($request)
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
          ->get();
        
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
            'data' => $products
        ], 200);
    }

    public function show(Product $product) {
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
}
