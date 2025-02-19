<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT clave FROM usuarios WHERE nombre = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user && password_verify($password, $user['clave']);
    }

    public function register($username, $password) {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, clave) VALUES (:username, :password)");
        return $stmt->execute(['nombre' => $username, 'clave' => $password]);
    }
}