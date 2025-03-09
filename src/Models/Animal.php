<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class Animal {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT idanimal, nombrecomun, nombrecientifico, idcategoria, foto, resumen, codusuario 
            FROM animales 
            WHERE idanimal = ?
        ");
        $stmt->execute([$id]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($animal && !empty($animal['foto'])) {
            $animal['foto'] = 'data:image/jpeg;base64,' . base64_encode($animal['foto']);
        }
    
        return $animal;
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM animales WHERE idanimal = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el animal: " . $e->getMessage());
        }
    }
    
    

    public function insert($nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen, $codusuario) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO animales (nombrecomun, nombrecientifico, idcategoria, foto, resumen, codusuario)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen, $codusuario]);
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el animal: " . $e->getMessage());
        }
    }

    public function update($id, $nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen) {
        try {
            $stmt = $this->db->prepare("
                UPDATE animales 
                SET nombrecomun = ?, nombrecientifico = ?, idcategoria = ?, foto = ?, resumen = ? 
                WHERE idanimal = ?
            ");
            return $stmt->execute([$nombrecomun, $nombrecientifico, $idcategoria, $foto, $resumen, $id]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el animal: " . $e->getMessage());
        }
    }
    
    

    public function getByCategory($categoryId) {
        $stmt = $this->db->prepare("SELECT idanimal, nombre, resumen, imagen FROM animales WHERE idcategoria = :idcategoria");
        $stmt->execute(['idcategoria' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaginated($limit, $offset) {
        $stmt = $this->db->prepare("
            SELECT a.idanimal, a.nombrecomun, a.nombrecientifico, a.foto, u.nombre AS usuario, a.codusuario
            FROM animales a
            JOIN usuarios u ON a.codusuario = u.codusuario
            ORDER BY a.idanimal ASC
            LIMIT :limit OFFSET :offset
        ");
    
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($animales as &$animal) {
            if (!empty($animal['foto'])) {
                $animal['foto'] = 'data:image/jpeg;base64,' . base64_encode($animal['foto']);
            }
        }
    
        return $animales;
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM animales");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
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

    public function updateTaxonomy($id, $renio, $clase, $orden, $familia, $genero, $especie) {
        $stmt = $this->db->prepare("
            UPDATE taxonomias 
            SET reino = :reino, clase = :clase, orden = :orden, 
                familia = :familia, genero = :genero, especie = :especie
            WHERE idanimal = :idanimal
        ");
        $stmt->execute([
            'reino' => $renio,
            'clase' => $clase,
            'orden' => $orden,
            'familia' => $familia,
            'genero' => $genero,
            'especie' => $especie,
            'idanimal' => $id
        ]);
    }
    

    public function existeNombre($nombre) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM animales WHERE nombrecomun = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    public function existeNombreCientifico($nombrecientifico) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM animales WHERE nombrecientifico = :nombrecientifico");
        $stmt->bindParam(':nombrecientifico', $nombrecientifico, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getDescription($idanimal) {
        $stmt = $this->db->prepare("
            SELECT titulo1, descripcion1, 
                   titulo2, descripcion2, 
                   titulo3, descripcion3, 
                   titulo4, descripcion4
            FROM descripciones
            WHERE idanimal = :idanimal
        ");
        $stmt->execute(['idanimal' => $idanimal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDescriptions($id, $titulo1, $descripcion1, $titulo2, $descripcion2, $titulo3, $descripcion3, $titulo4, $descripcion4) {
        $stmt = $this->db->prepare("
            UPDATE descripciones 
            SET titulo1 = :titulo1, descripcion1 = :descripcion1, 
                titulo2 = :titulo2, descripcion2 = :descripcion2, 
                titulo3 = :titulo3, descripcion3 = :descripcion3,
                titulo4 = :titulo4, descripcion4 = :descripcion4
            WHERE idanimal = :idanimal
        ");
        $stmt->execute([
            'titulo1' => $titulo1,
            'descripcion1' => $descripcion1,
            'titulo2' => $titulo2,
            'descripcion2' => $descripcion2,
            'titulo3' => $titulo3,
            'descripcion3' => $descripcion3,
            'titulo4' => $titulo4,
            'descripcion4' => $descripcion4,
            'idanimal' => $id
        ]);
    }
    
    
    
    
    
    
}
