<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\MenuItem\CategoryEnum;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $this->faker->randomElement([
                'Spring Rolls',
                'Chicken Wings',
                'Bruschetta',
                'Soup of the Day',
                'Grilled Salmon',
                'Beef Steak',
                'Nasi Goreng',
                'Pad Thai',
                'Chicken Curry',
                'Chocolate Lava Cake',
                'Ice Cream',
                'Mango Sticky Rice',
                'Fresh Lemonade',
                'Iced Coffee',
                'Mango Juice',
                'Green Tea',
            ]),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'category' => $this->faker->randomElement(CategoryEnum::cases()),
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
        ];
    }
}
