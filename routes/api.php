<?php

declare(strict_types=1);

use App\Http\Controllers\Api\MenuItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantController;

Route::apiResource('restaurants', RestaurantController::class);

Route::apiResource('restaurants.menu_items', MenuItemController::class)
    ->shallow()
    ->except(['show']);
