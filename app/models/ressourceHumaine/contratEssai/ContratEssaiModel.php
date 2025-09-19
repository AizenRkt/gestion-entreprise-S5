<?php
namespace app\models\ressourceHumaine\contratEssai;

use Flight;
use PDO;

class ContratEssaiModel {
    // Récupérer tous les contrats d'essai
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_essai ORDER BY debut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Récupérer par id_candidat
    public function getByCandidat($id_candidat) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_essai WHERE id_candidat = ?");
            $stmt->execute([$id_candidat]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Retourne tous les id_candidat sous contrat
    public function getAllCandidatIds() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT DISTINCT id_candidat FROM contrat_essai");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
