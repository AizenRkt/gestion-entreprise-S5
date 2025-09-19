<?php
namespace app\models\ressourceHumaine\typeResultatCandidat;

use Flight;
use PDO;

class TypeResultatCandidatModel {
    // Récupérer tous les types de résultat
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM type_resultat_candidat ORDER BY valeur ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
