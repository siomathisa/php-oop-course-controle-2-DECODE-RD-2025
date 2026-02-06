<?php

namespace App\Services;

use App\Entities\User;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) return false;

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        $_SESSION['user_id'] = $user->getId();
        return true;
    }

    public function register(string $name, string $email, string $password): bool
    {

        $existingUser = $this->userRepository->findByNameOrEmail($name, $email);

        if ($existingUser) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User($name, $email, $hashedPassword);
        $this->userRepository->create($user);

        return true;
    }

    public function logout()
    {
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): ?User
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->userRepository->findById($_SESSION['user_id']);
    }

    public function updateProfile(int $userId, string $name, string $email, ?string $password = null)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) return;

        $user->setName($name);
        $user->setEmail($email);

        if ($password) {
            $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        }

        $this->userRepository->update($user);
    }
}
