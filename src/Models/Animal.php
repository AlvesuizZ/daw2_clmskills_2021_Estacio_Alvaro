<?php
namespace Src\Models;

use Src\Models\Database;
use PDO;

class Animal {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByCategory($idcategoria) {
        $stmt = $this->db->prepare("SELECT idanimal, nombrecomun, nombrecientifico, resumen, foto FROM animals WHERE idcategoria = :idcategoria");
        $stmt->execute(['category_id' => $idcategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
