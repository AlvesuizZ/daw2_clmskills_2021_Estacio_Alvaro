<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class Category {
    private $db;

    public function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (\Throwable $th) {
            echo ("Error en la base de datos");
        }
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT idcategoria, nombre, foto FROM categorias ORDER BY nombre ASC");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

        foreach ($categorias as &$categoria) {
            if (!empty($categoria['foto'])) {
                $categoria['foto'] = 'data:image/jpeg;base64,' . base64_encode($categoria['foto']);
            } else {
                $categoria['foto'] = null;
            }
        }
    
        return $categorias;
    }
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE idcategoria = ?");
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($categoria && !empty($categoria['foto'])) {
            $categoria['foto'] = 'data:image/jpeg;base64,' . base64_encode($categoria['foto']);
        }
    
        return $categoria;
    }
    

    public function insert($nombre, $foto) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre, foto) VALUES (?, ?)");
        return $stmt->execute([$nombre, $foto]);
    }

    public function update($id, $nombre, $imagen = null) {
        if ($imagen) {
            $stmt = $this->db->prepare("UPDATE categorias SET nombre = ?, foto = ? WHERE idcategoria = ?");
            return $stmt->execute([$nombre, $imagen, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE categorias SET nombre = ? WHERE idcategoria = ?");
            return $stmt->execute([$nombre, $id]);
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE idcategoria = ?");
        return $stmt->execute([$id]);
    }

    public function deleteAnimalsByCategory($categoryId) {
        $stmt = $this->db->prepare("DELETE FROM animales WHERE idcategoria = :categoryId");
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
    }
    

    public function existeNombre($nombre) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorias WHERE nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
}

