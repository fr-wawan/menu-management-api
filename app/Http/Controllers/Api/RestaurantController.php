<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Restaurant\IndexRestaurantRequest;
use App\Http\Requests\Api\Restaurant\StoreRestaurantRequest;
use App\Http\Requests\Api\Restaurant\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantDetailResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Service\RestaurantService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class RestaurantController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly RestaurantService $restaurantService
    ) {}

    public function index(IndexRestaurantRequest $request): JsonResponse
    {
        return RestaurantResource::collection(
            $this->restaurantService->paginate(
                perPage: $request->integer('per_page', 15),
                search: $request->query('search'),
            )
        )->response();
    }

    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        return $this->success(
            new RestaurantResource($this->restaurantService->create($request->validated())),
            'Restaurant created successfully.',
            201
        );
    }

    public function show(Restaurant $restaurant): JsonResponse
    {
        return $this->success(
            new RestaurantDetailResource($restaurant->load('menuItems')),
            'Restaurant retrieved successfully.'
        );
    }

    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): JsonResponse
    {
        return $this->success(
            new RestaurantResource($this->restaurantService->update($restaurant, $request->validated())),
            'Restaurant updated successfully.'
        );
    }

    public function destroy(Restaurant $restaurant): JsonResponse
    {
        $this->restaurantService->delete($restaurant);

        return $this->success(
            null,
            'Restaurant deleted successfully.',
        );
    }
}
