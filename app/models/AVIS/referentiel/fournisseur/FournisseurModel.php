<?php
namespace app\models\AVIS\referentiel\fournisseur;
use Flight;
use PDO;
use Exception;

class FournisseurModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM fournisseur ORDER BY id_fournisseur DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_fournisseur) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM fournisseur WHERE id_fournisseur = :id_fournisseur");
            $stmt->execute([':id_fournisseur' => $id_fournisseur]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($nom, $adresse = null, $telephone = null, $email = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO fournisseur (nom, adresse, telephone, email) 
                VALUES (:nom, :adresse, :telephone, :email)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':adresse' => $adresse,
                ':telephone' => $telephone,
                ':email' => $email
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_fournisseur, $nom, $adresse = null, $telephone = null, $email = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE fournisseur 
                SET nom = :nom, adresse = :adresse, telephone = :telephone, email = :email 
                WHERE id_fournisseur = :id_fournisseur
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':adresse' => $adresse,
                ':telephone' => $telephone,
                ':email' => $email,
                ':id_fournisseur' => $id_fournisseur
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_fournisseur) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM fournisseur WHERE id_fournisseur = :id_fournisseur");
            $stmt->execute([':id_fournisseur' => $id_fournisseur]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function search($search_term) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT * FROM fournisseur 
                WHERE nom LIKE :search_term 
                OR email LIKE :search_term 
                OR telephone LIKE :search_term
                ORDER BY nom
            ");
            $search_pattern = '%' . $search_term . '%';
            $stmt->execute([':search_term' => $search_pattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
