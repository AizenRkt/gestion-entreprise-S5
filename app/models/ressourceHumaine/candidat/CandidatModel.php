<?php
namespace app\models\ressourceHumaine\candidat;

use Flight;
use PDO;

class CandidatModel {

    public function insert($nom, $prenom, $email, $telephone, $genre, $date_naissance) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance)
                VALUES (:nom, :prenom, :email, :telephone, :genre, :date_naissance)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':genre' => $genre,
                ':date_naissance' => $date_naissance
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM candidat ORDER BY date_candidature DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM candidat WHERE id_candidat = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $nom, $prenom, $email, $telephone, $genre) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE candidat 
                SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, genre = :genre
                WHERE id_candidat = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':genre' => $genre
            ]);
            return "Mise à jour réussie.";
        } catch (\PDOException $e) {
            return "Erreur de mise à jour : " . $e->getMessage();
        }
    }

    public function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM candidat WHERE id_candidat = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }
}
// ...fin du fichier sans accolades inutiles
