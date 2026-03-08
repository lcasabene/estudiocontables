<?php

namespace Core;

class Router
{
    private static array $routes = [];

    public static function get(string $path, callable|array $handler): void
    {
        self::$routes['GET'][$path] = $handler;
    }

    public static function post(string $path, callable|array $handler): void
    {
        self::$routes['POST'][$path] = $handler;
    }

    public static function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $routes = self::$routes[$method] ?? [];

        foreach ($routes as $pattern => $handler) {
            $regex = self::patternToRegex($pattern);
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    call_user_func_array([$controller, $action], $params);
                } else {
                    call_user_func_array($handler, $params);
                }
                return;
            }
        }

        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }

    private static function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }
}
