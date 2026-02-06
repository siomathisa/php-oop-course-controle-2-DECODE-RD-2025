<?php

namespace App\Http;

class Response
{
    private int $statusCode;
    private array $data;
    private ?string $message;

    public function __construct(array $data = [], int $statusCode = 200, ?string $message = null)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');

        $response = [];

        if ($this->message) {
            $response['message'] = $this->message;
        }

        if (!empty($this->data)) {
            $response['data'] = $this->data;
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(array $data = [], string $message = 'Success', int $statusCode = 200): self
    {
        return new self($data, $statusCode, $message);
    }

    public static function error(string $message, int $statusCode = 400, array $data = []): self
    {
        return new self($data, $statusCode, $message);
    }

    public static function created(array $data = [], string $message = 'Resource created successfully'): self
    {
        return new self($data, 201, $message);
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self([], 404, $message);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return new self([], 401, $message);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return new self([], 403, $message);
    }

    public static function serverError(string $message = 'Internal server error'): self
    {
        return new self([], 500, $message);
    }
}
