<?php
namespace app\models\AVIS\referentiel\client;
use Flight;
use PDO;
use Exception;

class ClientModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT c.*, ct.libelle AS type_libelle 
                FROM client c 
                LEFT JOIN client_type ct ON c.id_client_type = ct.id_client_type 
                ORDER BY c.id_client DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_client) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT c.*, ct.libelle AS type_libelle 
                FROM client c 
                LEFT JOIN client_type ct ON c.id_client_type = ct.id_client_type 
                WHERE c.id_client = :id_client
            ");
            $stmt->execute([':id_client' => $id_client]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($nom, $telephone = null, $email = null, $adresse = null, $id_client_type = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO client (nom, telephone, email, adresse, id_client_type) 
                VALUES (:nom, :telephone, :email, :adresse, :id_client_type)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':telephone' => $telephone,
                ':email' => $email,
                ':adresse' => $adresse,
                ':id_client_type' => $id_client_type
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_client, $nom, $telephone = null, $email = null, $adresse = null, $id_client_type = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE client 
                SET nom = :nom, telephone = :telephone, email = :email, adresse = :adresse, id_client_type = :id_client_type 
                WHERE id_client = :id_client
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':telephone' => $telephone,
                ':email' => $email,
                ':adresse' => $adresse,
                ':id_client_type' => $id_client_type,
                ':id_client' => $id_client
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_client) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM client WHERE id_client = :id_client");
            $stmt->execute([':id_client' => $id_client]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function search($search_term) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT c.*, ct.libelle AS type_libelle 
                FROM client c 
                LEFT JOIN client_type ct ON c.id_client_type = ct.id_client_type 
                WHERE c.nom LIKE :search_term 
                OR c.email LIKE :search_term 
                OR c.telephone LIKE :search_term
                ORDER BY c.nom
            ");
            $search_pattern = '%' . $search_term . '%';
            $stmt->execute([':search_term' => $search_pattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
