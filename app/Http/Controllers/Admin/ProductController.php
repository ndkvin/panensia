<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $products = Product::with(
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
        ->paginate($request->input('per_page', 10));
          
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    function createUniqueSlug($input)
    {
      $slug = Str::slug($input);
      $uniqueSlug = $slug;
      $counter = 1;
  
      while (Product::where('slug', $uniqueSlug)->exists()) {
        $uniqueSlug = $slug . '-' . $counter;
        $counter++;
      }

      return $uniqueSlug;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        $validate = Validator::make($request->all(), [
          'name' => 'required|string|min:3|max:55|unique:products,name',
          'description' => 'required|string',
          'category_id' => 'required|numeric|exists:categories,id'
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
        
        // create the product
        $product = Product::create([
          'name' => $request->name,
          'slug' => $this->createUniqueSlug($request->name),
          'description' => $request->description,
          'price' => $request->price,
          'category_id' => $request->category_id
        ]);

        return response()->json([
          'success' => true,
          'code' => 201,
          'message' => 'Product created',
          'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
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
    public function update(Request $request, Product $product)
    {
        // validate the request
        $validate = Validator::make($request->all(), [
          'name' => 'required|string|min:3|max:55|unique:products,name,'. $product->id,
          'description' => 'required|string',
          'category_id' => 'required|numeric|exists:categories,id'
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
        
        $data = $request->all();
        if ($request->name != $product->name) {
          $data['slug'] = $this->createUniqueSlug($request->name);
        }
        // update the product
        $product->update($data);

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Product updated',
          'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Product deleted',
          'data' => $product
        ]);
    }
}
