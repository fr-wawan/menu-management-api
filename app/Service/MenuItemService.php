<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\MenuItem;
use App\Models\Restaurant;

final class MenuItemService
{
    /**
     * @param array{name: string, description?: string|null, price: string, category?: string|null, is_available?: bool} $data
     */
    public function create(Restaurant $restaurant, array $data): MenuItem
    {
        return $restaurant->menuItems()->create($data);
    }

    /**
     * @param array{name?: string, description?: string|null, price?: string, category?: string|null, is_available?: bool} $data
     */
    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        $menuItem->update($data);

        return $menuItem->fresh();
    }

    public function delete(MenuItem $menuItem): void
    {
        $menuItem->delete();
    }

    public function listByRestaurant(
        Restaurant $restaurant,
        ?string $category = null,
        ?string $search = null,
        int $perPage = 15,
    ) {
        return $restaurant->menuItems()
            ->byCategory($category)
            ->search($search)
            ->paginate($perPage);
    }
}
