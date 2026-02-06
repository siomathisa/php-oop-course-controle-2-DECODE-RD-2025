<?php

namespace App\Repositories;

use App\Database\Database;
use App\Entities\Post;
use PDO;

class PostRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findById(int $id): ?Post
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return new Post(
            $data['title'],
            $data['content'],
            $data['user_id'],
            $data['id'],
            $data['created_at']
        );
    }

    public function findAll(int $limit, int $offset): array
    {
        $sql = "SELECT posts.id, posts.title, posts.created_at, users.name, users.id as user_id
                FROM posts 
                INNER JOIN users ON posts.user_id = users.id
                ORDER BY posts.created_at DESC
                LIMIT :limit
                OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): array
    {
        $sql = "SELECT posts.id, posts.title, posts.created_at
                FROM posts 
                INNER JOIN users ON posts.user_id = users.id
                WHERE posts.user_id = :id
                ORDER BY posts.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);

        return $stmt->fetchAll();
    }

    public function getPostWithAuthor(int $id): ?array
    {
        $sql = "SELECT posts.*, users.name, users.id as user_id
                FROM posts 
                INNER JOIN users ON posts.user_id = users.id
                WHERE posts.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        return $data ?: null;
    }

    public function create(Post $post): int
    {
        $sql = "INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'user_id' => $post->getUserId()
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        return (int) $stmt->fetchColumn();
    }
}
