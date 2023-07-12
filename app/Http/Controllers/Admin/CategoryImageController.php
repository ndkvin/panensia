<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryImageController extends Controller
{
    public function store(Request $request, Category $category) {

      $validate = Validator::make($request->all(), [
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

      if(!$category->image == null) {
        return response()->json([
          'success' => false,
          'code' => 400,
          'message' => 'Bad Request',
          'errors' => [
            'Image already set.'
          ]
        ], 400);
      } 

      $image = $request->file('image');
      $path = $image->store('images/categories', 'public');
      $category->image = $path;
      $category->save();
      return response()->json([
        'success' => true,
        'code' => 201,
        'message' => 'Category image uploaded successfully',
        'data' => [
          $category
        ]
      ], 201);

    }

    public function update(Request $request, Category $category) {
        $validate = Validator::make($request->all(), [
          'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
  
        if(!$category->image != null) {
          return response()->json([
            'success' => false,
            'code' => 400,
            'message' => 'Bad Request',
            'errors' => [
              'Image not set.'
            ]
          ], 400);
        }
        // delete old image
        Storage::delete('public/' . $category->image);

        // store new image
        $image = $request->file('image');
        $path = $image->store('images/categories', 'public');
        $category->image = $path;
        $category->save();

        return response()->json([
          'success' => true,
          'code' => 200,
          'message' => 'Category image updated successfully',
          'data' => [
            $category
          ]
        ], 200);
    }
}
