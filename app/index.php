<?php

require_once __DIR__ . '/init.php';

$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $httpMethod);
