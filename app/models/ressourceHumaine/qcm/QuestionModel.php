<?php
namespace app\models\QcmModel;

use Flight;
use PDO;

class QuestionModel {

    public function insert($enonce) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO question (enonce) VALUES (:enonce)");
            $stmt->execute([':enonce' => $enonce]);
            return "Question insérée avec succès.";
        } catch (\PDOException $e) {
            return "Erreur d'insertion : " . $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM question ORDER BY id_question DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM question WHERE id_question = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $enonce) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE question SET enonce = :enonce WHERE id_question = :id");
            $stmt->execute([
                ':id' => $id,
                ':enonce' => $enonce
            ]);
            return "Mise à jour réussie.";
        } catch (\PDOException $e) {
            return "Erreur de mise à jour : " . $e->getMessage();
        }
    }

    public function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM question WHERE id_question = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }
}
