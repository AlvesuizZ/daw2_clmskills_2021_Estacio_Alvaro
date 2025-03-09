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
        $this->routes['/contacto'] = ['controller' => 'ContactoController', 'action' => 'index'];
        $this->routes['/contactoPost'] = ['controller' => 'ContactoController', 'action' => 'enviar'];
        $this->routes['/quienesSomos'] = ['controller' => 'QuienesSomosController', 'action' => 'index'];
        $this->routes['/privacidad'] = ['controller' => 'PrivacidadController', 'action' => 'index'];
        $this->routes['/category/{id}'] = ['controller' => 'AnimalController', 'action' => 'showByCategory'];
        $this->routes['/categorias'] = ['controller' => 'CategoriaController', 'action' => 'index'];
        $this->routes['/createCategoria'] = ['controller' => 'CategoriaController', 'action' => 'create'];
        $this->routes['/createCategoriaPost'] = ['controller' => 'CategoriaController', 'action' => 'store'];
        $this->routes['/categoriaDelete/{id}'] = ['controller' => 'CategoriaController', 'action' => 'delete'];
        $this->routes['/editCategoria/{id}'] = ['controller' => 'CategoriaController', 'action' => 'edit'];
        $this->routes['/editCategoriaPost/{id}'] = ['controller' => 'CategoriaController', 'action' => 'update'];
        $this->routes['/gestionAnimals'] = ['controller' => 'AnimalController', 'action' => 'index'];
        $this->routes['/crearAnimal'] = ['controller' => 'AnimalController', 'action' => 'nuevo'];
        $this->routes['/crearAnimalPost'] = ['controller' => 'AnimalController', 'action' => 'guardar'];
        $this->routes['/editarAnimal/{id}'] = ['controller' => 'AnimalController', 'action' => 'editar'];
        $this->routes['/editarAnimalPost/{id}'] = ['controller' => 'AnimalController', 'action' => 'actualizar'];
        $this->routes['/deleteAnimal/{id}'] = ['controller' => 'AnimalController', 'action' => 'eliminar'];
        $this->routes['/api/categoria/{id}'] = ['controller' => 'AnimalController', 'action' => 'apiListByCategory'];
        $this->routes['/api/validarEmail'] = ['controller' => 'AuthController', 'action' => 'validarEmail'];
        $this->routes['/api/categorias/add'] = ['controller' => 'CategoriaController', 'action' => 'addAjax'];
        $this->routes['/api/categorias'] = ['controller' => 'CategoriaController', 'action' => 'listAjax'];
        $this->routes['/api/categorias/list'] = ['controller' => 'CategoriaController', 'action' => 'listAjax'];
        $this->routes['/animales/verificar-nombre'] = ['controller' => 'AnimalController', 'action' => 'verificarNombre'];
        $this->routes['/animales/verificar-nombrecientifico'] = ['controller' => 'AnimalController', 'action' => 'verificarNombreCientifico'];
        $this->routes['/categorias/verificar-nombre'] = ['controller' => 'CategoriaController', 'action' => 'verificarNombre'];



    }

    public function handleRequest()
    {
        $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        $originalPath = $path;
        error_log("Ruta original: " . $originalPath);
    
        $parts = explode('/', trim($path, '/'));
        $paramValue = null;
    

        if (is_numeric(end($parts))) {
            $paramValue = array_pop($parts); 
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
                    $controller->$action($paramValue);
                } else {
                    $controller->$action(); 
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
