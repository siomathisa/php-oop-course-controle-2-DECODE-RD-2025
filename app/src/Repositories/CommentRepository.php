<?php

namespace App\Repositories;

use App\Database\Database;
use App\Entities\Comment;
use PDO;

class CommentRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findById(int $id): ?Comment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return new Comment(
            $data['content'],
            $data['post_id'],
            $data['user_id'],
            $data['id'],
            $data['created_at']
        );
    }

    public function findByPostId(int $postId): array
    {
        $sql = "SELECT comments.*, users.name as user_name, users.id as user_id 
                FROM comments 
                INNER JOIN users ON comments.user_id = users.id 
                WHERE post_id = :post_id
                ORDER BY comments.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll();
    }

    public function create(Comment $comment)
    {
        $stmt = $this->pdo->prepare('INSERT INTO comments (content, post_id, user_id) VALUES (:content, :post_id, :user_id)');
        $stmt->execute([
            'content' => $comment->getContent(),
            'post_id' => $comment->getPostId(),
            'user_id' => $comment->getUserId()
        ]);

        $comment->setId((int) $this->pdo->lastInsertId());
    }
}
