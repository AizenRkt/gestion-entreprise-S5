<?php
namespace app\models\ressourceHumaine\cv;

use Flight;
use PDO;

class DetailCvModel {
    // Insérer un nouveau détail de CV
    public function insert($id_cv, $type, $id_item) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO detail_cv (id_cv, type, id_item)
                VALUES (:id_cv, :type, :id_item)
            ");
            $stmt->execute([
                ':id_cv' => $id_cv,
                ':type' => $type,
                ':id_item' => $id_item
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Erreur insertion detail_cv : ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer tous les détails d'un CV
    public function getByCv($id_cv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM detail_cv WHERE id_cv = ?");
            $stmt->execute([$id_cv]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Supprimer un détail de CV
    public function delete($id_detail_cv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM detail_cv WHERE id_detail_cv = ?");
            $stmt->execute([$id_detail_cv]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
