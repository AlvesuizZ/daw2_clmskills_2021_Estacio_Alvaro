<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Category;

class PrivacidadController {
    private $twig;
    private $categoryModel;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->categoryModel = new Category();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('privacidad.html.twig', [
            'user_id' => $_COOKIE['user_id'],
            'categories' => $categories]
        );
    }
}
