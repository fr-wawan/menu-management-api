<?php

declare(strict_types=1);

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('Restaurant API', function () {
    describe('GET /api/restaurants', function () {
        it('returns paginated list of restaurants', function () {
            Restaurant::factory()->count(3)->create();

            $response = $this->getJson('/api/restaurants');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => ['id', 'name', 'address', 'phone', 'opening_hours', 'created_at', 'updated_at'],
                    ],
                    'links',
                    'meta',
                ]);
        });

        it('returns empty list when no restaurants exist', function () {
            $response = $this->getJson('/api/restaurants');

            $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
        });

        it('can search restaurants by name', function () {
            Restaurant::factory()->create(['name' => 'Pizza Palace']);
            Restaurant::factory()->create(['name' => 'Burger King']);

            $response = $this->getJson('/api/restaurants?search=Pizza');

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Pizza Palace');
        });

        it('can paginate with custom per_page', function () {
            Restaurant::factory()->count(10)->create();

            $response = $this->getJson('/api/restaurants?per_page=5');

            $response->assertStatus(200)
                ->assertJsonCount(5, 'data')
                ->assertJsonPath('meta.per_page', 5);
        });
    });

    describe('GET /api/restaurants/{id}', function () {
        it('returns restaurant with menu items', function () {
            $restaurant = Restaurant::factory()
                ->has(MenuItem::factory()->count(3))
                ->create();

            $response = $this->getJson("/api/restaurants/{$restaurant->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'address',
                        'phone',
                        'opening_hours',
                        'menu_items' => [
                            '*' => ['id', 'name', 'description', 'price', 'category', 'is_available'],
                        ],
                    ],
                ])
                ->assertJsonCount(3, 'data.menu_items');
        });

        it('returns 404 when restaurant not found', function () {
            $response = $this->getJson('/api/restaurants/999');

            $response->assertStatus(404);
        });
    });

    describe('POST /api/restaurants', function () {
        it('creates restaurant when authenticated', function () {
            Sanctum::actingAs(User::factory()->create());

            $response = $this->postJson('/api/restaurants', [
                'name' => 'New Restaurant',
                'address' => '123 Main Street',
                'phone' => '+62-812-3456-7890',
                'opening_hours' => '09:00 - 22:00',
            ]);

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Restaurant created successfully.',
                ])
                ->assertJsonPath('data.name', 'New Restaurant');

            $this->assertDatabaseHas('restaurants', [
                'name' => 'New Restaurant',
                'address' => '123 Main Street',
            ]);
        });

        it('returns 401 when not authenticated', function () {
            $response = $this->postJson('/api/restaurants', [
                'name' => 'New Restaurant',
                'address' => '123 Main Street',
            ]);

            $response->assertStatus(401);
        });

        it('returns 422 when validation fails', function () {
            Sanctum::actingAs(User::factory()->create());

            $response = $this->postJson('/api/restaurants', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'address']);
        });

        it('validates name max length', function () {
            Sanctum::actingAs(User::factory()->create());

            $response = $this->postJson('/api/restaurants', [
                'name' => str_repeat('a', 256),
                'address' => '123 Main Street',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });

    describe('PUT /api/restaurants/{id}', function () {
        it('updates restaurant when authenticated', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()->create();

            $response = $this->putJson("/api/restaurants/{$restaurant->id}", [
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Restaurant updated successfully.',
                ])
                ->assertJsonPath('data.name', 'Updated Name');

            $this->assertDatabaseHas('restaurants', [
                'id' => $restaurant->id,
                'name' => 'Updated Name',
            ]);
        });

        it('returns 401 when not authenticated', function () {
            $restaurant = Restaurant::factory()->create();

            $response = $this->putJson("/api/restaurants/{$restaurant->id}", [
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(401);
        });

        it('returns 404 when restaurant not found', function () {
            Sanctum::actingAs(User::factory()->create());

            $response = $this->putJson('/api/restaurants/999', [
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(404);
        });
    });

    describe('DELETE /api/restaurants/{id}', function () {
        it('deletes restaurant when authenticated', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()->create();

            $response = $this->deleteJson("/api/restaurants/{$restaurant->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Restaurant deleted successfully.',
                ]);

            $this->assertDatabaseMissing('restaurants', [
                'id' => $restaurant->id,
            ]);
        });

        it('returns 401 when not authenticated', function () {
            $restaurant = Restaurant::factory()->create();

            $response = $this->deleteJson("/api/restaurants/{$restaurant->id}");

            $response->assertStatus(401);
        });

        it('cascades delete to menu items', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()
                ->has(MenuItem::factory()->count(3))
                ->create();

            $menuItemIds = $restaurant->menuItems->pluck('id')->toArray();

            $this->deleteJson("/api/restaurants/{$restaurant->id}");

            $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
            foreach ($menuItemIds as $menuItemId) {
                $this->assertDatabaseMissing('menu_items', ['id' => $menuItemId]);
            }
        });
    });
});
