<?php

session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Repositories\UserRepository;
use App\Repositories\PostRepository;
use App\Repositories\CommentRepository;
use App\Services\AuthService;
use App\Services\PostService;
use App\Services\CommentService;
use App\Controllers\Api\ApiAuthController;
use App\Controllers\Api\ApiPostController;
use App\Controllers\Api\ApiUserController;
use App\Controllers\Api\ApiCommentController;
use App\Router;

$userRepository = new UserRepository();
$postRepository = new PostRepository();
$commentRepository = new CommentRepository();

$authService = new AuthService($userRepository);
$postService = new PostService($postRepository);
$commentService = new CommentService($commentRepository);

// API Controllers
$apiAuthController = new ApiAuthController($authService);
$apiPostController = new ApiPostController($postService, $authService);
$apiUserController = new ApiUserController($userRepository);
$apiCommentController = new ApiCommentController($commentService, $authService);

$controllers = [
    'Api\\ApiAuthController' => $apiAuthController,
    'Api\\ApiPostController' => $apiPostController,
    'Api\\ApiUserController' => $apiUserController,
    'Api\\ApiCommentController' => $apiCommentController
];

$router = new Router(__DIR__ . '/config/routes.json', $controllers);
