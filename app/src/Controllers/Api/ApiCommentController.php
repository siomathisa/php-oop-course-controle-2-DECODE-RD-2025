<?php

namespace App\Controllers\Api;

use App\Http\Response;
use App\Services\AuthService;
use App\Services\CommentService;

class ApiCommentController
{
    private CommentService $commentService;
    private AuthService $authService;

    public function __construct(CommentService $commentService, AuthService $authService)
    {
        $this->commentService = $commentService;
        $this->authService = $authService;
    }

    public function index(): void
    {
        $postId = $_GET['post_id'] ?? null;

        if (!$postId) {
            Response::error('Post ID is required', 400)->send();
        }

        $comments = $this->commentService->getCommentsByPostId((int)$postId);

        Response::success(['comments' => $comments])->send();
    }

    public function store(): void
    {
        if (!$this->authService->isLoggedIn()) {
            Response::unauthorized('You must be logged in to post a comment')->send();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $content = $input['content'] ?? '';
        $postId = $input['post_id'] ?? null;

        if (empty($content) || !$postId) {
            Response::error('Content and post_id are required', 400)->send();
        }

        $user = $this->authService->getCurrentUser();
        $this->commentService->createComment($content, (int)$postId, $user->getId());

        Response::created([
            'comment' => [
                'content' => $content,
                'post_id' => $postId,
                'user_id' => $user->getId()
            ]
        ], 'Comment created successfully')->send();
    }
}
