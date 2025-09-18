<?php
namespace app\models\ressourceHumaine\diplome;

use Flight;
use PDO;

class DiplomeModel {
    // Récupérer tous les diplômes
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM diplome ORDER BY nom ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
