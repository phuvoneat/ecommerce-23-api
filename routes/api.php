<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//guest middleware 
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    //categories
    Route::get('categories', [CategoryController::class, 'index']); // Get all categories
    Route::post('categories', [CategoryController::class, 'store']); // Create new category
    Route::post('categories/{id}', [CategoryController::class, 'update']); // Update category
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']); // Delete category

    //products 
    Route::post('products', [ProductController::class, 'store']); // Create new product
    Route::post('products/{id}', [ProductController::class, 'update']); // Update product
    Route::delete('products/{id}', [ProductController::class, 'destroy']); // Delete product

    Route::get('me', [AuthController::class, 'me']); // Get current user
});
Route::get('products', [ProductController::class, 'index']); // Get all products
Route::get('product-by-category/{id}', [ProductController::class, 'getProductByCategory']); // Get all products by category
Route::post('searchProduct', [ProductController::class, 'searchProduct']); // Get all products by category

Route::post('/send-notification', [NotificationController::class, 'sendToUser']);
Route::post('/send-topic-notification', [NotificationController::class, 'sendToTopic']);