<?php

namespace App\controllers;

use Twig\Environment;
use PDO;
use App\Models\Database;
use Twig\Loader\FileSystemLoader;
use App\Models\Category;

class CategoriaController{

    private PDO $db;
    private $categoryModel;
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->db = Database::getInstance()->getConnection();
        $this->twig = new Environment($loader);
        $this->categoryModel = new Category();
    }

    public function index() {
        $user_id = $_COOKIE['user_id'] ?? null;
        echo $this->twig->render('categorias2.html.twig',  ['user_id' => $user_id]);
    }

    public function index2() {
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
        try {
            $this->categoryModel->deleteAnimalsByCategory($id);
            $this->categoryModel->delete($id);

            echo json_encode(["success" => true, "message" => "Categoría eliminada correctamente."]);
        } catch (\Exception $e) {
            echo json_encode(["success" => false, "message" => "Error al eliminar la categoría."]);
        }
    }
    
    

    public function addAjax() {
        header('Content-Type: application/json');
    
        $nombre = $_POST['nombre'] ?? '';
        $foto = $_FILES['foto'] ?? null;
    
        if (empty($nombre) || !$foto) {
            echo json_encode(['error' => 'Todos los campos son obligatorios.']);
            exit();
        }
    
        $imagenData = file_get_contents($foto['tmp_name']);
    
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre, foto) VALUES (?, ?)");
        $stmt->execute([$nombre, $imagenData]);
    
        echo json_encode(['success' => 'Categoría añadida correctamente.']);
        exit();
    }

    public function listAjax() {
        header('Content-Type: application/json');
    
        if (!$this->db) {
            error_log("Error: No hay conexión a la base de datos");
            echo json_encode(["error" => "No hay conexión a la base de datos"]);
            exit();
        }
    
        try {
            $stmt = $this->db->query("SELECT idcategoria, nombre, foto FROM categorias ORDER BY nombre ASC");
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (!$categorias) {
                error_log("Error: No hay categorías en la base de datos");
                echo json_encode(["error" => "No hay categorías disponibles"]);
                exit();
            }
    
            foreach ($categorias as &$categoria) {
                $categoria['foto'] = base64_encode($categoria['foto']);
            }
    
            echo json_encode($categorias);
            exit();
        } catch (\PDOException $e) {
            error_log("Error en consulta SQL: " . $e->getMessage());
            echo json_encode(["error" => "Error en la consulta SQL"]);
            exit();
        }
    }

    public function verificarNombre() {
        $nombre = $_GET['nombre'] ?? '';
        $existe = $this->categoryModel->existeNombre($nombre);
        echo json_encode(["exists" => $existe]);
    }
    
    
    
    
}



