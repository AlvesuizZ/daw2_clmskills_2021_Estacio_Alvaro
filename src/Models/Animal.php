<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class Animal {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByCategory($categoryId) {
        $stmt = $this->db->prepare("SELECT idanimal, nombre, resumen, imagen FROM animales WHERE idcategoria = :idcategoria");
        $stmt->execute(['idcategoria' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategoryPaginated($idcategoria, $limit, $offset){
        $stmt = $this->db->prepare("SELECT idanimal, nombrecomun, nombrecientifico, resumen, foto FROM animales WHERE idcategoria = :idcategoria LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByCategory($idcategoria) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM animales WHERE idcategoria = :idcategoria");
        $stmt->execute(['idcategoria' => $idcategoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTaxonomy($idanimal) {
        $stmt = $this->db->prepare("
            SELECT t.reino, t.filo, t.clase, t.orden, t.familia, t.genero, t.especie
            FROM taxonomias t
            JOIN animales a ON t.idanimal = a.idanimal
            WHERE a.idanimal = :idanimal
        ");
        $stmt->execute(['idanimal' => $idanimal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    
}
