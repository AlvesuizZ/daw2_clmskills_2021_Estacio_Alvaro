<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FileSystemLoader;
use App\Models\User;

class AuthController {

    private $twig;
    private $userModel;

    public function __construct() {
        session_start();
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        $this->userModel = new User();
    }

    public function showRegister() {
        $user_id = $_COOKIE['user_id'] ?? null;
        echo $this->twig->render('register.html.twig', ['user_id' => $user_id]);
    }

    public function showLogIn() {
        $user_id = $_COOKIE['user_id'] ?? null;
        echo $this->twig->render('login.html.twig', ['user_id' => $user_id]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($email, $password);
            if ($user) {
                error_log('a');
                setcookie('user_id', $user['codusuario'], time() + (7 * 24 * 60 * 60), "/", "", false, true);
                header("Location: /");
                exit;
            } else {
                echo "Credenciales incorrectas.";
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
                return;
            }

            if ($this->userModel->register($nombre, $direccion, $email, $password, $telef)) {
                header("Location: /login");
                exit;
            } else {
                echo "Error al registrar usuario.";
            }
        }
    }

    public function logout() {
        // Eliminamos la cookie de usuario
        setcookie('user_id', '', time() - 3600, "/", "", false, true);
        header("Location: /");
        exit;
    }
}