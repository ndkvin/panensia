<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::search($request)->paginate($request->input('per_page', 10));

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Category list',
          'data' => $categories->items(),
            'pagination' => [
              'current_page' => $categories->currentPage(),
              'last_page' => $categories->lastPage(),
              'per_page' => $categories->perPage(),
              'total' => $categories->total(),
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

    
    function createUniqueSlug($input)
    {
      $slug = Str::slug($input);
      $uniqueSlug = $slug;
      $counter = 1;
  
      while (Category::where('slug', $uniqueSlug)->exists()) {
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
        $validate = Validator::make($request->json()->all(), [
          'name' => 'required|min:3|max:55|string|unique:categories,name'
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
        
        
        // create the category
        $category = Category::create([
          'name' => $request->name,
          'slug' => $this->createUniqueSlug($request->name)
        ]);

        return response()->json([
          'success' => true,
          'code' => 201,
          'message' => 'Category created successfully',
          'data' =>  $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Category details',
          'data' => $category
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
    public function update(Request $request, Category $category)
    {
      $validate = Validator::make($request->json()->all(), [
        'name' => 'required|min:3|max:55|string|unique:categories,name,' . $category->id,
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

      // jika nama berbeda
      if ($request->name != $category->name) {
        $data['slug'] = $this->createUniqueSlug($request->name);
      }
  
      // update category
      $category->update($data);
      return response()->json([
        'success' => true,
        'code' => 200,
        'message' => 'Category updated successfully',
        'data' => $category
        
      ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {   
        if($category->products()->count() > 0) {
          return response()->json([
            'success' => false,
            'code' => 400,
            'message' => 'Bad Request',
            'errors' => [
              'Category cannot be deleted because it has products'
            ]
          ], 400);
        }

        // if has image
        if ($category->image != null) {
            Storage::delete('public/' . $category->image);
        }

        $category->delete();
        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Category deleted successfully'
        ], 200);
    }
}
