<?php
namespace app\models\ressourceHumaine\competence;

use Flight;
use PDO;

class CompetenceModel {
    // Récupérer toutes les compétences
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM competence ORDER BY nom ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
