<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use App\Models\Animal;
use App\Models\Category;

class AnimalController {
    private $twig;
    private $animalModel;
    private $categoryModel;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->animalModel = new Animal();
        $this->categoryModel = new Category();
    }

    public function showByCategory($idcategoria) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
    
        $animals = $this->animalModel->getByCategoryPaginated($idcategoria, $limit, $offset);
        $totalanimals = $this->animalModel->countByCategory($idcategoria);
        $totalPages = ceil($totalanimals / $limit);
    
        foreach ($animals as &$animal) {
            if (!empty($animal['foto'])) {
                $animal['foto'] = 'data:image/jpeg;base64,' . base64_encode($animal['foto']);
            }
        
            if (isset($animal['idanimal'])) {
                $animal['taxonomia'] = $this->animalModel->getTaxonomy($animal['idanimal']) ?? [];
                error_log("Taxonomía de {$animal['nombrecomun']}: " . print_r($animal['taxonomia'], true));
            }
        }

        $categories = $this->categoryModel->getAll();
        $user_id = $_COOKIE['user_id'] ?? null;
    
        echo $this->twig->render('animals.html.twig', [
            'user' => $_SESSION['user'] ?? null,
            'animals' => $animals,
            'categories' => $categories,
            'idcategoria' => $idcategoria,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'user_id' => $user_id
        ]);
    }
    

}
