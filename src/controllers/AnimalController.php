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

    public function index() {
        session_start();
        if (!isset($_COOKIE['user_id'])) {
            header("Location: /login");
            exit();
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $animals = $this->animalModel->getPaginated($limit, $offset);
        $totalAnimales = $this->animalModel->countAll();
        $totalPages = ceil($totalAnimales / $limit);

        echo $this->twig->render('GestionAnimals.html.twig', [
            'animals' => $animals,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'user_id' => $_COOKIE['user_id']
        ]);
    }

    public function nuevo() {
        session_start();
        if (!isset($_COOKIE['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('crearAnimal.html.twig', [
            'categories' => $categories,
            'user_id' => $_COOKIE['user_id']
        ]);
    }
    
    public function guardar() {
        session_start();
        if (!isset($_COOKIE['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombrecomun = trim($_POST['nombrecomun']);
                $nombrecientifico = trim($_POST['nombrecientifico']);
                $idcategoria = $_POST['idcategoria'] ?? null;
                $resumen = trim($_POST['resumen']);
                $codusuario = $_COOKIE['user_id'];
    
                if (empty($nombrecomun) || empty($nombrecientifico) || empty($idcategoria) || empty($resumen) || empty($_FILES['foto']['tmp_name'])) {
                    throw new Exception("Todos los campos son obligatorios.");
                }
    
                $foto = file_get_contents($_FILES['foto']['tmp_name']);
    
                $this->animalModel->insert($nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen, $codusuario);
    
                $_SESSION['success'] = "Animal añadido correctamente.";
                header("Location: /gestionAnimals");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /crearAnimal");
            exit();
        }
    }
    

    public function editar($id) {
        session_start();
        if (!isset($_COOKIE['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        $animal = $this->animalModel->find($id);
        $categories = $this->categoryModel->getAll();
    
        if (!$animal || $animal['codusuario'] != $_COOKIE['user_id']) {
            $_SESSION['error'] = "No tienes permiso para modificar este animal.";
            header("Location: /gestionAnimals");
            exit();
        }
    
        echo $this->twig->render('editarAnimal.html.twig', [
            'animal' => $animal,
            'categories' => $categories,
            'user_id' => $_COOKIE['user_id']
        ]);
    }
    
    public function actualizar($id) {
        session_start();
        if (!isset($_COOKIE['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombrecomun = trim($_POST['nombrecomun']);
                $nombrecientifico = trim($_POST['nombrecientifico']);
                $idcategoria = $_POST['idcategoria'] ?? null;
                $resumen = trim($_POST['resumen']);
                $codusuario = $_COOKIE['user_id']; // Usuario actual
    
                $animal = $this->animalModel->find($id);
    
                if (!$animal || $animal['codusuario'] != $codusuario) {
                    throw new Exception("No tienes permiso para modificar este animal.");
                }
    
                if (empty($nombrecomun) || empty($nombrecientifico) || empty($idcategoria) || empty($resumen)) {
                    throw new Exception("Todos los campos son obligatorios.");
                }
    
                
                $foto = isset($_FILES['foto']) && $_FILES['foto']['error'] === 0 
                    ? file_get_contents($_FILES['foto']['tmp_name']) 
                    : $animal['foto'];
    
                $this->animalModel->update($id, $nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen);
    
                $_SESSION['success'] = "Animal actualizado correctamente.";
                header("Location: /gestionAnimals");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /admin/animales/editar/$id");
            exit();
        }
    }

    public function eliminar($id) {
        session_start();
        if (!isset($_COOKIE['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        try {
            $animal = $this->animalModel->find($id);
    
            if (!$animal) {
                throw new Exception("El animal no existe.");
            }
    
            if ($animal['codusuario'] != $_COOKIE['user_id']) {
                throw new Exception("No tienes permiso para eliminar este animal.");
            }
    
            $this->animalModel->delete($id);
    
            $_SESSION['success'] = "Animal eliminado correctamente.";
            header("Location: /gestionAnimals");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /gestionAnimals");
            exit();
        }
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

    public function apiListByCategory($idcategoria) {
        header('Content-Type: application/json');
    
        $page = $_GET['page'] ?? 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
    
        $animales = $this->animalModel->getByCategoryPaginated($idcategoria, $limit, $offset);
        $totalAnimales = $this->animalModel->countByCategory($idcategoria);
        $totalPages = ceil($totalAnimales / $limit);
    
        echo json_encode([
            'animales' => $animales,
            'totalPages' => $totalPages
        ]);
        exit();
    }

    public function verificarNombre() {
        $nombre = $_GET['nombre'] ?? '';
        $existe = $this->animalModel->existeNombre($nombre);
        echo json_encode(["exists" => $existe]);
    }
    
    public function verificarNombreCientifico() {
        $nombrecientifico = $_GET['nombrecientifico'] ?? '';
        $existe = $this->animalModel->existeNombreCientifico($nombrecientifico);
        echo json_encode(["exists" => $existe]);
    }
    
    
    
    

}
