<?php

namespace App\Models;

use App\Models\Database;
use PDO;

class Contacto {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function insert($nombre, $email, $mensaje, $registrado) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO contactos (nombre, email, comentario, registrado) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$nombre, $email, $mensaje, $registrado]);
        } catch (PDOException $e) {
            throw new \Exception("Error al guardar el mensaje: " . $e->getMessage());
        }
    }
    
}
