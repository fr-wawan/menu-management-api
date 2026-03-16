<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MenuItem\IndexMenuItemRequest;
use App\Http\Requests\Api\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\Api\MenuItem\UpdateMenuItemRequest;
use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\MenuItemService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class MenuItemController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly MenuItemService $menuItemService) {}

    public function index(IndexMenuItemRequest $request, Restaurant $restaurant): JsonResponse
    {
        $items = $this->menuItemService->listByRestaurant(
            restaurant: $restaurant,
            category: $request->query('category'),
            search: $request->query('search'),
            perPage: $request->integer('per_page', 15)
        );

        return MenuItemResource::collection($items)->response();
    }

    public function store(StoreMenuItemRequest $request, Restaurant $restaurant): JsonResponse
    {
        return $this->success(
            new MenuItemResource($this->menuItemService->create($restaurant, $request->validated())),
            'Menu item created successfully.',
            201
        );
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): JsonResponse
    {
        return $this->success(
            new MenuItemResource($this->menuItemService->update($menuItem, $request->validated())),
            'Menu item updated successfully.'
        );
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $this->menuItemService->delete($menuItem);

        return $this->success(
            null,
            'Menu item deleted successfully.',
        );
    }
}
