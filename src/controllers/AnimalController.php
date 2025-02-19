<?php
namespace Src\Controllers;

use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use Src\Models\Animal;
use Src\Models\Category;

class AnimalController {
    private $twig;
    private $animalModel;
    private $categoryModel;

    public function __construct(Environment $twig) {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->animalModel = new Animal();
        $this->categoryModel = new Category();
    }

    public function showByCategory($categoryId) {
        $animals = $this->animalModel->getByCategory($categoryId);
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('animals.twig', [
            'user' => $_SESSION['user'] ?? null,
            'categories' => $categories,
            'animals' => $animals
        ]);
    }
}
