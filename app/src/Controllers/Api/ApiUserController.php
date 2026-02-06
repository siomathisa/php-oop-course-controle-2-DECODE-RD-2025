<?php

namespace App\Controllers\Api;

use App\Http\Response;
use App\Repositories\UserRepository;

class ApiUserController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            Response::error('User ID is required', 400)->send();
        }

        $user = $this->userRepository->findById((int)$id);

        if (!$user) {
            Response::notFound('User not found')->send();
        }

        Response::success([
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'created_at' => $user->getCreatedAt()
            ]
        ])->send();
    }
}
