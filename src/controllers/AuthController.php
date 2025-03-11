<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use App\Models\User;
use App\Models\Database;
use App\Models\Category;

class AuthController {

    private $twig;
    private $userModel;
    private $db;
    private $categoryModel;

    public function __construct() {
        session_start();
        $this->db = Database::getInstance()->getConnection();
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->userModel = new User();
        $this->categoryModel = new Category();
    }

    public function showRegister() {
        session_start();
        $categories = $this->categoryModel->getAll();
        $user_id = $_SESSION['user_id'] ?? null;
        echo $this->twig->render('register.html.twig', ['user_id' => $user_id, 'categories' => $categories]);
    }

    public function showLogIn() {
        session_start();
        $categories = $this->categoryModel->getAll();
        $user_id = $_SESSION['user_id'] ?? null;
        echo $this->twig->render('login.html.twig', ['user_id' => $user_id, 'categories' => $categories]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($email, $password);
            if ($user) {
                error_log('a');
                //setcookie('user_id', $user['codusuario'], time() + (7 * 24 * 60 * 60), "/", "", false, true);
                $_SESSION['user_id'] = $user['codusuario'];
                header("Location: /");
                exit;
            } else {
                echo $this->twig->render('login.html.twig', [
                    'error' => 'Credenciales incorrectas.',
                ]);
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $telef = $_POST['telef'] ?? '';
            error_log($nombre);
            error_log($direccion);
            error_log($email);
            error_log($password);
            error_log($telef);

            if (empty($nombre) || empty($direccion) || empty($email) || empty($password) || empty($telef)) {
                echo "Todos los campos son obligatorios.";
                exit;
            }

            if ($this->userModel->register($nombre, $direccion, $email, $password, $telef)) {
                header("Location: /login");
                exit;
            } else {
                echo "Error al registrar usuario.";
            }
        }
    }

    public function validarEmail() {
        header('Content-Type: application/json');
    
        $email = $_GET['email'] ?? '';
        if (empty($email)) {
            echo json_encode(['exists' => false]);
            exit();
        }
    
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
    
        echo json_encode(['exists' => $count > 0]);
        exit();
    }
    

    public function logout() {
        session_destroy();
        header("Location: /");
        exit;
    }
}