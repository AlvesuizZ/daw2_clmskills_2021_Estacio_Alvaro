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
        $this->routes['/login'] = ['controller' => 'AuthController', 'action' => 'showlogin'];
        $this->routes['/loginPost'] = ['controller' => 'AuthController', 'action' => 'login'];
        $this->routes['/register'] = ['controller' => 'AuthController', 'action' => 'showRegister'];
        $this->routes['/registerPost'] = ['controller' => 'AuthController', 'action' => 'register'];
        $this->routes['/logout'] = ['controller' => 'AuthController', 'action' => 'logout'];
        $this->routes['/category/{id}'] = ['controller' => 'AnimalController', 'action' => 'showByCategory'];
        $this->routes['/categorias'] = ['controller' => 'CategoriaController', 'action' => 'index'];
        $this->routes['/createCategoria'] = ['controller' => 'CategoriaController', 'action' => 'create'];
        $this->routes['/createCategoriaPost'] = ['controller' => 'CategoriaController', 'action' => 'store'];
        $this->routes['/categoriaDelete/{id}'] = ['controller' => 'CategoriaController', 'action' => 'delete'];
        $this->routes['/editCategoria/{id}'] = ['controller' => 'CategoriaController', 'action' => 'edit'];
        $this->routes['/editCategoriaPost/{id}'] = ['controller' => 'CategoriaController', 'action' => 'update'];
    }

    public function handleRequest()
    {
        $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        $originalPath = $path;
        error_log("Ruta original: " . $originalPath);
    
        $parts = explode('/', trim($path, '/'));
        $paramValue = null;
    
        // Verificar si el último segmento es un número (ID)
        if (is_numeric(end($parts))) {
            $paramValue = array_pop($parts); // Extraer el ID
            $path = '/' . implode('/', $parts) . '/{id}'; // Convertir la ruta a formato dinámico
        }
    
        error_log("Ruta procesada: " . $path);
    
        if (isset($this->routes[$path])) {
            $route = $this->routes[$path];
            $controllerClass = 'App\\Controllers\\' . $route['controller'];
            error_log("Cargando controlador: " . $controllerClass);
            
            $action = $route['action'];
    
            if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
                $controller = new $controllerClass();
    
                if ($paramValue !== null) {
                    error_log("Parámetro ID: " . $paramValue);
                    $controller->$action($paramValue); // Pasar el ID al controlador
                } else {
                    $controller->$action(); // Sin ID
                }
            } else {
                http_response_code(404);
                echo '404 - Controlador o acción no encontrados';
            }
        } else {
            http_response_code(404);
            echo '404 - Ruta no encontrada';
        }
    }
    
}

$route = new Router();

$route->handleRequest();
