<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile'])->middleware('auth:api');

Route::get('/', function () {
  return response()->json([
    'success' => false,
    'code' => 403,
    'message' => [
      'You are not allowed to access this route'
    ]
  ], 403);
})->name('login');
