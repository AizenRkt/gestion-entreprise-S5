<?php
namespace app\models\ressourceHumaine\qcm\QcmModel;

use Flight;
use PDO;

class QcmModel {

    public function insert($id_annonce, $titre, $note_max) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO qcm (id_annonce, titre, note_max) VALUES (:id_annonce, :titre, :note_max)");
            $stmt->execute([
                ':id_annonce' => $id_annonce,
                ':titre' => $titre,
                ':note_max' => $note_max
            ]);
            return "QCM inséré avec succès.";
        } catch (\PDOException $e) {
            return "Erreur d'insertion : " . $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM qcm ORDER BY date_creation DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM qcm WHERE id_qcm = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $id_annonce, $titre, $note_max) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE qcm SET id_annonce = :id_annonce, titre = :titre, note_max = :note_max WHERE id_qcm = :id");
            $stmt->execute([
                ':id' => $id,
                ':id_annonce' => $id_annonce,
                ':titre' => $titre,
                ':note_max' => $note_max
            ]);
            return "Mise à jour réussie.";
        } catch (\PDOException $e) {
            return "Erreur de mise à jour : " . $e->getMessage();
        }
    }

    public function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM qcm WHERE id_qcm = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }
}
