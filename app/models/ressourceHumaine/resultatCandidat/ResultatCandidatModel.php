<?php
namespace app\models\ressourceHumaine\resultatCandidat;

use Flight;
use PDO;

class ResultatCandidatModel {
    // Récupérer tous les résultats candidats
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM resultat_candidat ORDER BY date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Récupérer par id_candidat
    public function getByCandidat($id_candidat) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM resultat_candidat WHERE id_candidat = ?");
            $stmt->execute([$id_candidat]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
