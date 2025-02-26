<?php

namespace App\controllers;

use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use App\Models\Category;

class CategoriaController{

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->categoryModel = new Category();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        $user_id = $_COOKIE['user_id'] ?? null;
        echo $this->twig->render('categorias.html.twig', ['categories' => $categories, 'user_id' => $user_id]);
    }

    public function create() {
        $user_id = $_COOKIE['user_id'] ?? null;
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('createCategory.html.twig', ['categories' => $categories, 'user_id' => $user_id]);
    }

    public function store() {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = $_POST['nombre'] ?? '';
                $foto = $_FILES['foto'] ?? null;
    
                if (empty($nombre) || !$foto || $foto['error'] !== 0) {
                    echo "Error: Todos los campos son obligatorios.";
                    return;
                }
    
                $imagenBlob = file_get_contents($foto['tmp_name']);
                $this->categoryModel->insert($nombre, $imagenBlob);
    
                header('Location: /categorias');
                exit;
            }
        } catch (\Exception $e) {
            echo $this->twig->render('categorias.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit($id) {
        $categories = $this->categoryModel->getAll();
        $user_id = $_COOKIE['user_id'] ?? null;
        $categoria = $this->categoryModel->find($id);
        if (!$categoria) {
            echo "Categoría no encontrada";
            return;
        }

        echo $this->twig->render('editCategoria.html.twig', ['categories' => $categories, 'categoria' => $categoria, 'user_id' => $user_id]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $imagen = $_FILES['foto'] ?? null;
            $imagenBlob = null;

            if ($imagen && $imagen['error'] === 0) {
                $imagenBlob = file_get_contents($imagen['tmp_name']);
            }

            $this->categoryModel->update($id, $nombre, $imagenBlob);

            header('Location: /categorias');
            exit;
        }
    }

    public function delete($id) {
        $this->categoryModel->delete($id);
        header('Location: /categorias');
        exit;
    }
}



