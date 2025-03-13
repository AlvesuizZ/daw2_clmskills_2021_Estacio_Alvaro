<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Category;

class QuienesSomosController {
    private $twig;
    private $categoryModel;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->categoryModel = new Category();
    }

    public function index() {
        session_start();
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('quienesSomos.html.twig', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'categories' => $categories
            
        ]);
    }
}
