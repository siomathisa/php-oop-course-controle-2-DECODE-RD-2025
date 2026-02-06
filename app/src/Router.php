<?php

namespace App;

class Router
{
    private array $routes;
    private array $controllers;

    public function __construct(string $routesFile, array $controllers)
    {
        $this->controllers = $controllers;
        $this->loadRoutes($routesFile);
    }

    private function loadRoutes(string $routesFile): void
    {
        if (!file_exists($routesFile)) {
            throw new \Exception("Routes file not found: {$routesFile}");
        }

        $json = file_get_contents($routesFile);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON in routes file: " . json_last_error_msg());
        }

        $this->routes = $data['routes'] ?? [];
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['path'] === $uri && $route['method'] === $method) {
                $this->executeRoute($route);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page not found";
    }

    private function executeRoute(array $route): void
    {
        $controllerName = $route['controller'];
        $action = $route['action'];

        if (!isset($this->controllers[$controllerName])) {
            throw new \Exception("Controller not found: {$controllerName}");
        }

        $controller = $this->controllers[$controllerName];

        if (!method_exists($controller, $action)) {
            throw new \Exception("Method {$action} not found in controller {$controllerName}");
        }

        $controller->$action();
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
