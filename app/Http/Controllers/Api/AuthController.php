<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Service\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->success($result, 'User registered successfully.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (! $result) {
            return $this->error('Invalid credentials.', 401);
        }

        return $this->success($result, 'Login successful.');
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $this->authService->logout($user);

        return $this->success(null, 'Logged out successfully.');
    }
}
