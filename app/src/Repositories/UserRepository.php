<?php

namespace App\Repositories;

use App\Database\Database;
use App\Entities\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return new User(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['id'],
            $data['created_at']
        );
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();
        if (!$data) return null;

        return new User(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['id'],
            $data['created_at']
        );
    }

    public function findByNameOrEmail(string $name, string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email OR name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email, 'name' => $name]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new User(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['id'],
            $data['created_at']
        );
    }

    public function create(User $user)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->execute([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]);

        $user->setId((int) $this->pdo->lastInsertId());
    }

    public function update(User $user)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id");
        $stmt->execute([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'id' => $user->getId()
        ]);
    }
}
