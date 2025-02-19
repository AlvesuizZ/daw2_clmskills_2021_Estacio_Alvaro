<?php
namespace Src\Models;

use Src\Models\Database;
use PDO;

class Animal {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByCategory($categoryId) {
        $stmt = $this->db->prepare("SELECT id, name, image, summary FROM animals WHERE category_id = :category_id");
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
