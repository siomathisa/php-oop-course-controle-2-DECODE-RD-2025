<?php

namespace App\Controllers\Api;

use App\Http\Response;
use App\Services\AuthService;
use App\Services\PostService;

class ApiPostController
{
    private PostService $postService;
    private AuthService $authService;

    public function __construct(PostService $postService, AuthService $authService)
    {
        $this->postService = $postService;
        $this->authService = $authService;
    }

    public function index(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

        $posts = $this->postService->getPaginatedPosts($page, $limit);
        $pagination = $this->postService->getPagination($page, $limit);

        Response::success([
            'posts' => $posts,
            'pagination' => $pagination
        ])->send();
    }

    public function show(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            Response::error('Post ID is required', 400)->send();
        }

        $post = $this->postService->getPostWithAuthor((int)$id);

        if (!$post) {
            Response::notFound('Post not found')->send();
        }

        Response::success(['post' => $post])->send();
    }

    public function store(): void
    {
        if (!$this->authService->isLoggedIn()) {
            Response::unauthorized('You must be logged in to create a post')->send();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $title = $input['title'] ?? '';
        $content = $input['content'] ?? '';

        if (empty($title) || empty($content)) {
            Response::error('Title and content are required', 400)->send();
        }

        $user = $this->authService->getCurrentUser();
        $postId = $this->postService->createPost($title, $content, $user->getId());

        Response::created([
            'post' => [
                'id' => $postId,
                'title' => $title,
                'content' => $content,
                'user_id' => $user->getId()
            ]
        ], 'Post created successfully')->send();
    }

    public function userPosts(): void
    {
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            Response::error('User ID is required', 400)->send();
        }

        $posts = $this->postService->getUserPosts((int)$userId);

        Response::success(['posts' => $posts])->send();
    }
}
