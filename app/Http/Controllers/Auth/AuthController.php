<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $validate = Validator::make($request->all(), [
      'name' => 'required|min:3|max:55',
      'email' => 'email|required|unique:users',
      'password' => 'required|min:8',
      'phone' => 'required|min:10|max:12'
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
    $data['password'] = bcrypt($data['password']);

    $user = User::create($data);

    $accessToken = $user->createToken('authToken')->accessToken;

    return response([
      'success' => true,
      'code' => 200,
      'message' => 'User created successfully',
      'data' => [
        'access_token' => $accessToken
      ]
    ]);
  }

  public function login(Request $request)
  {
    $validate = Validator::make($request->all(), [
      'email' => 'email|required',
      'password' => 'required|min:8'
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

    if (!auth()->attempt($request->all())) {
      return response()->json([
        'success' => false,
        'code' => 400,
        'message' => 'Bad Request',
        'errors' => ['credential not match to our record']
      ], 400);
    }

    $accessToken = auth()->user()->createToken('authToken')->accessToken;

    return response(
      [
        'success' => true,
        'code' => 200,
        'message' => 'User login successfully',
        'data' => [
          'access_token' => $accessToken
        ]
      ]
    );
  }

  public function profile(Request $request)
  {
    $user = Auth::guard('api')->user();

    if ($user) {
      return response()->json([
        'success' => true,
        'code' => 200,
        'message' => 'User profile successfully',
        'data' => $user
      ]);
    }
  }
}
