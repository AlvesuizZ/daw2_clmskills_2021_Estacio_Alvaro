<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Database;

class AnimalController {
    private $twig;
    private $animalModel;
    private $categoryModel;
    private $db;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->animalModel = new Animal();
        $this->categoryModel = new Category();
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $animals = $this->animalModel->getPaginated($limit, $offset);
        $totalAnimales = $this->animalModel->countAll();
        $totalPages = ceil($totalAnimales / $limit);
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('GestionAnimals.html.twig', [
            'animals' => $animals,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'categories' => $categories,
            'user_id' => $_SESSION['user_id'],
            'success' => $_COOKIE['success'] ?? null
        ]);
    }

    public function nuevo() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        $categories = $this->categoryModel->getAll();
        echo $this->twig->render('crearAnimal.html.twig', [
            'categories' => $categories,
            'user_id' => $_SESSION['user_id']
        ]);
    }
    
    public function guardar() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
            try {
                $db = Database::getInstance()->getConnection();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {       

                    $nombrecomun = $_POST['nombrecomun'];
                    $nombrecientifico = $_POST['nombrecientifico'];
                    $idcategoria = $_POST['idcategoria'];
                    $codusuario = $_SESSION['user_id'];
        

                    $foto = null;
                    if (!empty($_FILES['foto']['tmp_name'])) {
                        $foto = file_get_contents($_FILES['foto']['tmp_name']);
                    }
        

                    $stmt = $db->prepare("INSERT INTO animales (nombrecomun, nombrecientifico, idcategoria, foto, codusuario) 
                                        VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nombrecomun, $nombrecientifico, $idcategoria, $foto, $codusuario]);

        

                    $idanimal = $db->lastInsertId();
        

                    $stmt = $db->prepare("INSERT INTO taxonomias (idanimal, reino, filo, clase, orden, familia, genero, especie) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $idanimal,
                        $_POST['reino'], $_POST['filo'], $_POST['clase'],
                        $_POST['orden'], $_POST['familia'], $_POST['genero'], $_POST['especie']
                    ]);
        

                    $stmt = $db->prepare("INSERT INTO descripciones (idanimal, titulo1, descripcion1, titulo2, descripcion2, titulo3, descripcion3, titulo4, descripcion4) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $idanimal,
                        $_POST['titulo1'], $_POST['descripcion1'],
                        $_POST['titulo2'], $_POST['descripcion2'],
                        $_POST['titulo3'], $_POST['descripcion3'],
                        $_POST['titulo4'], $_POST['descripcion4']
                    ]);
        

                    header("Location: /gestionAnimals?success=Animal agregado correctamente");
                    exit;
                }
            } catch (\Exception $e) {
                die("Error al insertar el animal: " . $e->getMessage());
            }
        }
        
    
    

        public function editar($id) {
            session_start();
            if (!isset($_SESSION['user_id'])) {
                header("Location: /login");
                exit();
            }

            $animal = $this->animalModel->find($id);

            $categories = $this->categoryModel->getAll();

            if (!$animal || $animal['codusuario'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "No tienes permiso para modificar este animal.";
                header("Location: /gestionAnimals");
                exit();
            }

            $taxonomia = $this->animalModel->getTaxonomy($id);

            $descripciones = $this->animalModel->getDescription($id);
        
        
    
        echo $this->twig->render('editarAnimal.html.twig', [
            'animal' => $animal,
            'categories' => $categories,
            'taxonomia' => $taxonomia,
            'descripciones' => $descripciones,
            'user_id' => $_SESSION['user_id']
        ]);
    }
    
    public function actualizar($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombrecomun = trim($_POST['nombrecomun']);
                $nombrecientifico = trim($_POST['nombrecientifico']);
                $idcategoria = $_POST['idcategoria'] ?? null;
                $resumen = trim($_POST['resumen']);
                $codusuario = $_SESSION['user_id']; 

                $renio = $_POST['renio'];
                $clase = $_POST['clase'];
                $orden = $_POST['orden'];
                $familia = $_POST['familia'];
                $genero = $_POST['genero'];
                $especie = $_POST['especie'];

                $titulo1 = $_POST['titulo1'];
                $descripcion1 = $_POST['descripcion1'];
                $titulo2 = $_POST['titulo2'];
                $descripcion2 = $_POST['descripcion2'];
                $titulo3 = $_POST['titulo3'];
                $descripcion3 = $_POST['descripcion3'];
                $titulo4 = $_POST['titulo4'];
                $descripcion4 = $_POST['descripcion4'];
    
                $animal = $this->animalModel->find($id);
                if (!$animal || $animal['codusuario'] != $codusuario) {
                    throw new \Exception("No tienes permiso para modificar este animal.");
                }
    
                if (empty($nombrecomun) || empty($nombrecientifico) || empty($idcategoria) || empty($resumen)) {
                    throw new \Exception("Todos los campos obligatorios deben ser completados.");
                }

                $foto = isset($_FILES['foto']) && $_FILES['foto']['error'] === 0 
                    ? file_get_contents($_FILES['foto']['tmp_name']) 
                    : $animal['foto']; 
 
                $this->animalModel->update($id, $nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen);
    
                $this->animalModel->updateTaxonomy($id, $renio, $clase, $orden, $familia, $genero, $especie);

                $this->animalModel->updateDescriptions($id, $titulo1, $descripcion1, $titulo2, $descripcion2, $titulo3, $descripcion3, $titulo4, $descripcion4);
    
                $_SESSION['success'] = "Animal actualizado correctamente.";
                header("Location: /gestionAnimals");
                exit();
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /admin/animales/editar/$id");
            exit();
        }
    }
    

    public function eliminar($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    
        try {
            $animal = $this->animalModel->find($id);
    
            if (!$animal) {
                throw new \Exception("El animal no existe.");
            }
    
            if ($animal['codusuario'] != $_SESSION['user_id']) {
                throw new \Exception("No tienes permiso para eliminar este animal.");
            }
    
            $this->animalModel->delete($id);
    
            setcookie("success", "❌ Animal eliminado correctamente", time() + 5, "/");
            header("Location: /gestionAnimals");
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /gestionAnimals");
            exit();
        }
    }
    
    

    public function showByCategory($idcategoria) {
        session_start();
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
                $animal['descripcion'] = $this->animalModel->getDescription($animal['idanimal']) ?? [];
            }
        }
    
        $categories = $this->categoryModel->getAll();
        $user_id = $_SESSION['user_id'] ?? null;
    
        echo $this->twig->render('animals.html.twig', [
            'user' => $_SESSION['user_id'] ?? null,
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
        $idAnimal = $_GET['id'] ?? null; // En caso de edición
    
        $existe = $this->animalModel->existeNombre($nombre, $idAnimal);
        echo json_encode(["exists" => $existe]);
    }
    
    public function verificarNombreCientifico() {
        $nombrecientifico = $_GET['nombrecientifico'] ?? '';
        $existe = $this->animalModel->existeNombreCientifico($nombrecientifico);
        echo json_encode(["exists" => $existe]);
    }
    
    
    
    

}
