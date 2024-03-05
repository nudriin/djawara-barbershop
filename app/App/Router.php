<?php

namespace Nurdin\Djawara\App;

require_once __DIR__ . "/../../vendor/autoload.php";

class Router
{
    private static array $routes = [];

    public static function add(string $method, string $path, string $controller, string $function, array $middlewares = []): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'function' => $function,
            'middleware' => $middlewares
        ];
    }

    public static function run(): void
    {
        $path = "/";
        if (isset($_SERVER['PATH_INFO'])) $path = $_SERVER['PATH_INFO'];
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            $pattern = "#^" . $route['path'] . "$#";
            if (preg_match($pattern, $path, $variables) && $method == $route['method']) {
                // call middleware
                foreach ($route['middleware'] as $middleware) {
                    $instance = new $middleware;
                    $instance->auth();
                }

                $controller = new $route['controller'];
                $function = $route['function'];
                // $controller->$function();

                array_shift($variables);
                call_user_func_array([$controller, $function], $variables);
                return;
            }
        }

        header('Location: /');
        // $_SERVER['PATH_INFO'] = "/";
        // http_response_code(404);
        // echo '<h1 class = "text-center">404 NOT FOUND</h1>' . PHP_EOL;
    }
}
