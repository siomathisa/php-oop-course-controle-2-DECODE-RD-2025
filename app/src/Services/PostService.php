<?php

namespace App\Services;

use App\Entities\Post;
use App\Repositories\PostRepository;

class PostService
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function createPost(string $title, string $content, int $userId): int
    {
        $post = new Post($title, $content, $userId);
        return $this->postRepository->create($post);
    }

    public function getPostWithAuthor(int $id): ?array
    {
        return $this->postRepository->getPostWithAuthor($id);
    }

    public function getPaginatedPosts(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        return $this->postRepository->findAll($limit, $offset);
    }

    public function getUserPosts(int $userId): array
    {
        return $this->postRepository->findByUserId($userId);
    }

    public function getPagination(int $page, int $limit): array
    {
        $total = $this->postRepository->count();
        $pages = ceil($total / $limit);

        return [
            'pagesCount' => $pages,
            'currentPage' => $page,
        ];
    }
}
