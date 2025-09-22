<?php
namespace app\models\ressourceHumaine\employe;

use Flight;
use PDO;

class EmployeModel {
    // Retourne tous les id_candidat present dans employe
    public function getAllEmployeIds() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT DISTINCT id_candidat FROM employe");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Récupérer tous les résultats candidats
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM employe ORDER BY id_employe ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Récupérer par employe
    public function getByEmploye($id_employe) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM employe WHERE id_employe = ?");
            $stmt->execute([$id_employe]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
