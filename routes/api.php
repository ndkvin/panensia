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

Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::get('/profile', [App\Http\Controllers\Auth\AuthController::class, 'profile'])->middleware('auth:api');

Route::get('/unauthorize', function () {
  return response()->json([
    'success' => false,
    'code' => 403,
    'message' => [
      'You are not allowed to access this route'
    ]
  ], 403);
})->name('login');

Route::group([
  'middleware' => ['auth:api', 'can:admin'],
  'prefix' => 'admin',
  'as' => 'admin.',
], function () {
  Route::resource('category', CategoryController::class)->except(['edit', 'create']);
  Route::post('category/{category}/image', [App\Http\Controllers\Admin\CategoryImageController::class, 'store']);
  Route::post('category/{category}/image/edit', [App\Http\Controllers\Admin\CategoryImageController::class, 'update']);
  Route::resource('product', App\Http\Controllers\Admin\ProductController::class)->except(['edit', 'create']);
  Route::resource('/product/{product}/image', App\Http\Controllers\Admin\ProductImageController::class)->only(['store', 'destroy']);
  Route::resource('/product/{product}/type',App\Http\Controllers\Admin\ProductTypeController::class)->only(['store', 'destroy', 'update']);
});

Route::group([
  'middleware' => ['auth:api', 'can:user'],
  'prefix' => 'user',
  'as' => 'user.',
], function () {
  Route::resource('cart', App\Http\Controllers\User\CartController::class)->except(['edit', 'create', 'show']);
  Route::resource('order', App\Http\Controllers\User\OrderController::class)->except(['edit', 'create', 'destroy', 'update']);
  Route::resource('payment', App\Http\Controllers\User\PaymentController::class)->except(['edit', 'create', 'show']);
  Route::post('/payment/available', [App\Http\Controllers\User\PaymentController::class, 'available']);
  Route::resource('address', App\Http\Controllers\User\AddressController::class)->except(['edit', 'create']);
});

Route::get('/product', [App\Http\Controllers\HomeController::class, 'product']);
Route::get('/product/{product}', [App\Http\Controllers\HomeController::class, 'showProduct']);
Route::get('/category', [App\Http\Controllers\HomeController::class, 'category']);