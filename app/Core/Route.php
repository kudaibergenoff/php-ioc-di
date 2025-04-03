<?php

namespace App\Core;

class Route
{
    private static array $routes = [];

    public static function get(string $uri, callable|array $action): void
    {
        self::addRoute('GET', $uri, $action);
    }

    public static function post(string $uri, callable|array $action): void
    {
        self::addRoute('POST', $uri, $action);
    }

    public static function put(string $uri, callable|array $action): void
    {
        self::addRoute('PUT', $uri, $action);
    }

    public static function delete(string $uri, callable|array $action): void
    {
        self::addRoute('DELETE', $uri, $action);
    }

    private static function addRoute(string $method, string $uri, callable|array $action): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $uri);
        self::$routes[$method][$pattern] = $action;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes[$method] ?? [] as $route => $action) {
            if (preg_match("#^$route$#", $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $response = self::executeAction($action, $params);

                // Если есть результат, выводим его
                if (!is_null($response)) {
                    echo $response;
                }

                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private static function executeAction(callable|array $action, array $params): mixed
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        } elseif (is_array($action)) {
            [$controller, $method] = $action;
            if (class_exists($controller) && method_exists($controller, $method)) {
                return call_user_func_array([new $controller, $method], $params);
            } else {
                throw new \Exception("Controller or method not found");
            }
        }

        return null;
    }
}
