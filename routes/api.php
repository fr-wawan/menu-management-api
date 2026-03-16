<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::get('restaurants', [RestaurantController::class, 'index']);
Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show']);
Route::get('restaurants/{restaurant}/menu_items', [MenuItemController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    // Restaurant management
    Route::post('restaurants', [RestaurantController::class, 'store']);
    Route::put('restaurants/{restaurant}', [RestaurantController::class, 'update']);
    Route::delete('restaurants/{restaurant}', [RestaurantController::class, 'destroy']);

    // Menu item management
    Route::post('restaurants/{restaurant}/menu_items', [MenuItemController::class, 'store']);
    Route::put('menu_items/{menu_item}', [MenuItemController::class, 'update']);
    Route::delete('menu_items/{menu_item}', [MenuItemController::class, 'destroy']);
});
