<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\MenuItem\CategoryEnum;
use App\Models\MenuItem;
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
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'category' => $this->faker->randomElement(CategoryEnum::cases()),
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
        ];
    }
}
