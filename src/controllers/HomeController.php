<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
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
            if (!empty($category['foto'])) {
                $img = imagecreatefromstring($category['foto']);
                if ($img !== false) {
                    ob_start();
                    imagewebp($img, null, 80); // Convertir a WebP con calidad del 80%
                    $webpData = ob_get_clean();
                    imagedestroy($img);
                    $category['foto_base64'] = 'data:image/webp;base64,' . base64_encode($webpData);
                } else {
                    $category['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($category['foto']); // Fallback a JPEG si falla
                }
            }
        }

        echo $this->twig->render('home.html.twig', ['user_id' => $user_id, 'categories' => $categories]);
    }
    
}