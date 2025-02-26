<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use App\Models\Category;

class HomeController {
    private $twig;
    private $categoryModel;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->categoryModel = new Category();
    }


    public function index() {
        session_start();

        $user_id = $_COOKIE['user_id'] ?? null;
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('home.html.twig', ['user_id' => $user_id, 'categories' => $categories]);
    }
}