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
    
        $user_id = $_SESSION['user_id'] ?? null;

        $categories = $this->categoryModel->getAll();

        foreach ($categories as &$category) {
            if ($category['foto']) {
                $category['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($category['foto']);
            }
        }

        echo $this->twig->render('home.html.twig', ['user_id' => $user_id, 'categories' => $categories]);
    }
    
}