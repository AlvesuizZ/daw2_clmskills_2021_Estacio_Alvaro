<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['clave'])) {
            return $user;
        }
        return false;
    }

    public function register($nombre, $direccion, $email, $password, $telef) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $fechaalta = date('Y-m-d');
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, direccion, email, clave, telef, fechaalta) 
            VALUES (:nombre, :direccion, :email, :clave, :telef, :fechaalta)
        ");
        
        return $stmt->execute([
            'nombre' => $nombre,
            'direccion' => $direccion,
            'email' => $email,
            'clave' => $hashedPassword,
            'telef' => $telef,
            'fechaalta' => $fechaalta
        ]);
    }
}