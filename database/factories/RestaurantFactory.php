<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['The', 'Cafe', 'Restaurant', 'Warung'])
                . ' ' . $this->faker->lastName(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->numerify('+62-8##-####-####'),
            'opening_hours' => $this->faker->randomElement([
                '08:00 - 22:00',
                '09:00 - 21:00',
                '10:00 - 23:00',
                '07:00 - 20:00',
            ]),
        ];
    }
}
