<?php
namespace app\models\QcmModel;

use Flight;
use PDO;

class ReponseModel {

    public function insert($id_question, $texte, $est_correcte = false) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO reponse (id_question, texte, est_correcte) VALUES (:id_question, :texte, :est_correcte)");
            $stmt->execute([
                ':id_question' => $id_question,
                ':texte' => $texte,
                ':est_correcte' => $est_correcte
            ]);
            return "Réponse insérée avec succès.";
        } catch (\PDOException $e) {
            return "Erreur d'insertion : " . $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM reponse ORDER BY id_reponse DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM reponse WHERE id_reponse = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getByQuestion($id_question) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM reponse WHERE id_question = ?");
            $stmt->execute([$id_question]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function update($id, $id_question, $texte, $est_correcte) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE reponse SET id_question = :id_question, texte = :texte, est_correcte = :est_correcte WHERE id_reponse = :id");
            $stmt->execute([
                ':id' => $id,
                ':id_question' => $id_question,
                ':texte' => $texte,
                ':est_correcte' => $est_correcte
            ]);
            return "Mise à jour réussie.";
        } catch (\PDOException $e) {
            return "Erreur de mise à jour : " . $e->getMessage();
        }
    }

    public function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM reponse WHERE id_reponse = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }
}
