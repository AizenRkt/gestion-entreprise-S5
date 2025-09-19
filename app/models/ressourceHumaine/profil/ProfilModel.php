<?php
namespace app\models\ressourceHumaine\profil;

use Flight;
use PDO;

class ProfilModel {
    // Récupérer tous les profils
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM profil ORDER BY nom ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
