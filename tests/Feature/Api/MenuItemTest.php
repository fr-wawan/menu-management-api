<?php

declare(strict_types=1);

use App\Enum\MenuItem\CategoryEnum;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('MenuItem API', function () {
    describe('GET /api/restaurants/{restaurant}/menu_items', function () {
        it('returns paginated list of menu items', function () {
            $restaurant = Restaurant::factory()
                ->has(MenuItem::factory()->count(5))
                ->create();

            $response = $this->getJson("/api/restaurants/{$restaurant->id}/menu_items");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => ['id', 'name', 'description', 'price', 'category', 'is_available', 'created_at', 'updated_at'],
                    ],
                    'links',
                    'meta',
                ])
                ->assertJsonCount(5, 'data');
        });

        it('returns empty list when restaurant has no menu items', function () {
            $restaurant = Restaurant::factory()->create();

            $response = $this->getJson("/api/restaurants/{$restaurant->id}/menu_items");

            $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
        });

        it('can filter by category', function () {
            $restaurant = Restaurant::factory()->create();
            MenuItem::factory()->create([
                'restaurant_id' => $restaurant->id,
                'category' => CategoryEnum::APPETIZER,
            ]);
            MenuItem::factory()->create([
                'restaurant_id' => $restaurant->id,
                'category' => CategoryEnum::MAIN_COURSE,
            ]);

            $response = $this->getJson("/api/restaurants/{$restaurant->id}/menu_items?category=appetizer");

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'appetizer');
        });

        it('can search by name', function () {
            $restaurant = Restaurant::factory()->create();
            MenuItem::factory()->create([
                'restaurant_id' => $restaurant->id,
                'name' => 'Grilled Salmon',
            ]);
            MenuItem::factory()->create([
                'restaurant_id' => $restaurant->id,
                'name' => 'Beef Steak',
            ]);

            $response = $this->getJson("/api/restaurants/{$restaurant->id}/menu_items?search=Salmon");

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Grilled Salmon');
        });

        it('returns 404 when restaurant not found', function () {
            $response = $this->getJson('/api/restaurants/999/menu_items');

            $response->assertStatus(404);
        });

        it('validates category enum', function () {
            $restaurant = Restaurant::factory()->create();

            $response = $this->getJson("/api/restaurants/{$restaurant->id}/menu_items?category=invalid");

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
    });

    describe('POST /api/restaurants/{restaurant}/menu_items', function () {
        it('creates menu item when authenticated', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()->create();

            $response = $this->postJson("/api/restaurants/{$restaurant->id}/menu_items", [
                'name' => 'Spring Rolls',
                'description' => 'Crispy vegetable spring rolls',
                'price' => 8.99,
                'category' => 'appetizer',
                'is_available' => true,
            ]);

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Menu item created successfully.',
                ])
                ->assertJsonPath('data.name', 'Spring Rolls');

            $this->assertDatabaseHas('menu_items', [
                'restaurant_id' => $restaurant->id,
                'name' => 'Spring Rolls',
            ]);
        });

        it('returns 401 when not authenticated', function () {
            $restaurant = Restaurant::factory()->create();

            $response = $this->postJson("/api/restaurants/{$restaurant->id}/menu_items", [
                'name' => 'Spring Rolls',
                'price' => 8.99,
            ]);

            $response->assertStatus(401);
        });

        it('returns 422 when validation fails', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()->create();

            $response = $this->postJson("/api/restaurants/{$restaurant->id}/menu_items", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'price']);
        });

        it('validates price format', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()->create();

            $response = $this->postJson("/api/restaurants/{$restaurant->id}/menu_items", [
                'name' => 'Test Item',
                'price' => -5.00,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['price']);
        });

        it('validates category enum value', function () {
            Sanctum::actingAs(User::factory()->create());
            $restaurant = Restaurant::factory()->create();

            $response = $this->postJson("/api/restaurants/{$restaurant->id}/menu_items", [
                'name' => 'Test Item',
                'price' => 10.00,
                'category' => 'invalid_category',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
        });
    });

    describe('PUT /api/menu_items/{id}', function () {
        it('updates menu item when authenticated', function () {
            Sanctum::actingAs(User::factory()->create());
            $menuItem = MenuItem::factory()->create();

            $response = $this->putJson("/api/menu_items/{$menuItem->id}", [
                'name' => 'Updated Name',
                'price' => 15.99,
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Menu item updated successfully.',
                ])
                ->assertJsonPath('data.name', 'Updated Name')
                ->assertJsonPath('data.price', 15.99);

            $this->assertDatabaseHas('menu_items', [
                'id' => $menuItem->id,
                'name' => 'Updated Name',
                'price' => 15.99,
            ]);
        });

        it('returns 401 when not authenticated', function () {
            $menuItem = MenuItem::factory()->create();

            $response = $this->putJson("/api/menu_items/{$menuItem->id}", [
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(401);
        });

        it('returns 404 when menu item not found', function () {
            Sanctum::actingAs(User::factory()->create());

            $response = $this->putJson('/api/menu_items/999', [
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(404);
        });

        it('can update availability', function () {
            Sanctum::actingAs(User::factory()->create());
            $menuItem = MenuItem::factory()->create(['is_available' => true]);

            $response = $this->putJson("/api/menu_items/{$menuItem->id}", [
                'is_available' => false,
            ]);

            $response->assertStatus(200)
                ->assertJsonPath('data.is_available', false);
        });
    });

    describe('DELETE /api/menu_items/{id}', function () {
        it('deletes menu item when authenticated', function () {
            Sanctum::actingAs(User::factory()->create());
            $menuItem = MenuItem::factory()->create();

            $response = $this->deleteJson("/api/menu_items/{$menuItem->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Menu item deleted successfully.',
                ]);

            $this->assertDatabaseMissing('menu_items', [
                'id' => $menuItem->id,
            ]);
        });

        it('returns 401 when not authenticated', function () {
            $menuItem = MenuItem::factory()->create();

            $response = $this->deleteJson("/api/menu_items/{$menuItem->id}");

            $response->assertStatus(401);
        });

        it('returns 404 when menu item not found', function () {
            Sanctum::actingAs(User::factory()->create());

            $response = $this->deleteJson('/api/menu_items/999');

            $response->assertStatus(404);
        });
    });
});
