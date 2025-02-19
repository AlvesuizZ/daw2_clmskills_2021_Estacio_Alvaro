<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $user = new User();
            if ($user->authenticate($username, $password)) {
                $_SESSION['user'] = $username;
                header('Location: /');
                exit();
            } else {
                echo "<p>Usuario o contraseña incorrectos</p>";
            }
        }
        include __DIR__ . '/../Views/login.html.twig';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            
            $user = new User();
            if ($user->register($username, $password)) {
                header('Location: /login');
                exit();
            } else {
                echo "<p>Error al registrar usuario</p>";
            }
        }
        include __DIR__ . '/../Views/register.html.twig';
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit();
    }
}