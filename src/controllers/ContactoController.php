<?php

namespace App\Controllers;

use App\Models\Contacto;
use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use App\Models\Category;

class ContactoController {
    private $twig;
    private $contactoModel;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->contactoModel = new Contacto();
        $this->categoryModel = new Category();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('contacto.html.twig', [
            'user_id' => $_COOKIE['user_id'],
            'categories' => $categories
        ]);
    }

    public function enviar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                session_start();
                $nombre = trim($_POST['nombre']);
                $email = trim($_POST['email']);
                $comentario = trim($_POST['comentario']);
    
                if (empty($nombre) || empty($email) || empty($comentario)) {
                    throw new \Exception("Todos los campos son obligatorios.");
                }
    
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("El email no es válido.");
                }
    
                
                $registrado = isset($_COOKIE['user_id']) ? 1 : 0;
    
                $this->contactoModel->insert($nombre, $email, $comentario, $registrado);
                $categories = $this->categoryModel->getAll();
                echo $this->twig->render('contacto.html.twig', [
                    'success' => "Mensaje enviado correctamente.",
                    'user_id' => $_COOKIE['user_id'],
                    'categories' => $categories
                ]);
                return;
            }
        } catch (\Exception $e) {
            echo $this->twig->render('contacto.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
}
