<?php

namespace App\Services;

use App\Entities\Comment;
use App\Repositories\CommentRepository;

class CommentService
{
    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function createComment(string $content, int $postId, int $userId)
    {
        $comment = new Comment($content, $postId, $userId);
        $this->commentRepository->create($comment);
    }

    public function getCommentsByPostId(int $postId): array
    {
        return $this->commentRepository->findByPostId($postId);
    }
}
