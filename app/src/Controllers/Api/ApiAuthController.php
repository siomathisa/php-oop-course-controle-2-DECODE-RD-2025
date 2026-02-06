<?php

namespace App\Controllers\Api;

use App\Http\Response;
use App\Services\AuthService;

class ApiAuthController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::error('Email and password are required', 400)->send();
        }

        if ($this->authService->login($email, $password)) {
            $user = $this->authService->getCurrentUser();
            Response::success([
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                ]
            ], 'Login successful')->send();
        }

        Response::unauthorized('Invalid credentials')->send();
    }

    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            Response::error('Name, email and password are required', 400)->send();
        }

        if ($this->authService->register($name, $email, $password)) {
            Response::created([], 'User registered successfully')->send();
        }

        Response::error('User already exists or registration failed', 409)->send();
    }

    public function logout(): void
    {
        $this->authService->logout();
        Response::success([], 'Logout successful')->send();
    }

    public function profile(): void
    {
        if (!$this->authService->isLoggedIn()) {
            Response::unauthorized('You must be logged in')->send();
        }

        $user = $this->authService->getCurrentUser();

        if (!$user) {
            Response::notFound('User not found')->send();
        }

        Response::success([
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ]
        ])->send();
    }

    public function updateProfile(): void
    {
        if (!$this->authService->isLoggedIn()) {
            Response::unauthorized('You must be logged in')->send();
        }

        $user = $this->authService->getCurrentUser();
        $input = json_decode(file_get_contents('php://input'), true);

        $name = $input['name'] ?? $user->getName();
        $email = $input['email'] ?? $user->getEmail();
        $password = $input['password'] ?? null;

        $this->authService->updateProfile($user->getId(), $name, $email, $password);

        Response::success([
            'user' => [
                'id' => $user->getId(),
                'name' => $name,
                'email' => $email
            ]
        ], 'Profile updated successfully')->send();
    }
}
