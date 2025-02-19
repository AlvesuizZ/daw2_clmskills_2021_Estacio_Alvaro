<?php

namespace App\Routes;
class Router{
    private $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    public function loadRoutes()
    {
        $this->routes['/'] = ['controller' => 'HomeController', 'action' => 'index'];
        $this->routes['/login'] = ['controller' => 'AuthController', 'action' => 'login'];
        $this->routes['/register'] = ['controller' => 'AuthController', 'action' => 'register'];
        $this->routes['/logout'] = ['controller' => 'AuthController', 'action' => 'logout'];
        $this->routes['/category/{id}'] = ['controller' => 'AnimalController', 'action' => 'showByCategory'];
    }

    public function handleRequest()
    {
        $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        if (isset($this->routes[$path])) {
            $route           = $this->routes[$path];
            $controllerClass = 'App\\controllers\\' . $route['controller'];
            error_log($controllerClass);
            $action = $route['action'];
            if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
                $controller = new $controllerClass();
                $controller->$action();
            } else {
                http_response_code(404);
                echo '404';
            }
        } else {
            http_response_code(404);
            echo '404';
        }
    }
}

$route = new Router();

$route->handleRequest();
