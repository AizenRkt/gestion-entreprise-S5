<?php
namespace app\models\ressourceHumaine\paie;

use Flight;
use PDO;
use PDOException;

class PaieModel {

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function tauxAssurance(): array {
        try {
            $sql = "SELECT * FROM assurance";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
}