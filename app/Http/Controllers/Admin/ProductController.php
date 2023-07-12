<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Product list',
          'data' => Product::all()
        ]);
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
        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Product detail',
          'data' => $product
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
    public function destroy(Product $product)
    {
        //
    }
}
