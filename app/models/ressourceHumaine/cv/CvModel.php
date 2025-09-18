<?php
namespace app\models\ressourceHumaine\cv;

use Flight;
use PDO;

class CvModel {
    // Insérer un nouveau CV
    public function insert($id_candidat, $id_profil, $photo = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO cv (id_candidat, id_profil, photo)
                VALUES (:id_candidat, :id_profil, :photo)
            ");
            $stmt->execute([
                ':id_candidat' => $id_candidat,
                ':id_profil' => $id_profil,
                ':photo' => $photo
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Erreur insertion CV : ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer tous les CV
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM cv ORDER BY date_soumission DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Récupérer un CV par ID
    public function getById($id_cv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM cv WHERE id_cv = ?");
            $stmt->execute([$id_cv]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    // Supprimer un CV
    public function delete($id_cv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM cv WHERE id_cv = ?");
            $stmt->execute([$id_cv]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
