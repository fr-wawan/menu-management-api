<?php

declare(strict_types=1);

use App\Models\User;

describe('Auth API', function () {
    describe('POST /api/auth/register', function () {
        it('registers a new user with valid data', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => ['id', 'name', 'email'],
                        'token',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'email' => 'john@example.com',
            ]);
        });

        it('fails with invalid email', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'email' => 'invalid-email',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('fails when email already exists', function () {
            User::factory()->create(['email' => 'existing@example.com']);

            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('fails when password confirmation does not match', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });

        it('fails when required fields are missing', function () {
            $response = $this->postJson('/api/auth/register', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
        });
    });

    describe('POST /api/auth/login', function () {
        it('logs in with valid credentials', function () {
            $user = User::factory()->create([
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/auth/login', [
                'email' => 'john@example.com',
                'password' => 'password123',
            ]);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => ['id', 'name', 'email'],
                        'token',
                    ],
                ]);
        });

        it('fails with invalid credentials', function () {
            User::factory()->create([
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/auth/login', [
                'email' => 'john@example.com',
                'password' => 'wrongpassword',
            ]);

            $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ]);
        });

        it('fails with non-existent email', function () {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'password123',
            ]);

            $response->assertStatus(401);
        });

        it('fails when required fields are missing', function () {
            $response = $this->postJson('/api/auth/login', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
        });
    });

    describe('POST /api/auth/logout', function () {
        it('logs out authenticated user', function () {
            $user = User::factory()->create();
            $token = $user->createToken('auth_token')->plainTextToken;

            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->postJson('/api/auth/logout');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Logged out successfully.',
                ]);

            // Token should be deleted
            $this->assertDatabaseMissing('personal_access_tokens', [
                'tokenable_id' => $user->id,
            ]);
        });

        it('fails when not authenticated', function () {
            $response = $this->postJson('/api/auth/logout');

            $response->assertStatus(401);
        });
    });
});
