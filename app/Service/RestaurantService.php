<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Restaurant;

final class RestaurantService
{
    public function paginate(int $perPage = 15, ?string $search = null)
    {
        return Restaurant::search($search)->paginate($perPage);
    }

    /**
     * @param  array{name: string, address: string, phone?: string|null, opening_hours?: string|null}  $data
     */
    public function create(array $data): Restaurant
    {
        return Restaurant::create($data);
    }

    /**
     * @param  array{name?: string, address?: string, phone?: string|null, opening_hours?: string|null}  $data
     */
    public function update(Restaurant $restaurant, array $data): Restaurant
    {
        $restaurant->update($data);

        return $restaurant;
    }

    public function delete(Restaurant $restaurant): void
    {
        $restaurant->delete();
    }
}
