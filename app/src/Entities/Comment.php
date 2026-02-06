<?php

namespace App\Entities;

class Comment
{
    private ?int $id = null;
    private string $content;
    private int $postId;
    private int $userId;
    private ?string $createdAt = null;

    public function __construct(string $content, int $postId, int $userId, ?int $id = null, ?string $createdAt = null)
    {
        $this->content = $content;
        $this->postId = $postId;
        $this->userId = $userId;
        $this->id = $id;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt
        ];
    }
}
