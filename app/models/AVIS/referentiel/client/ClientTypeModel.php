<?php
namespace app\models\AVIS\referentiel\client;
use Flight;
use PDO;
use Exception;

class ClientTypeModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM client_type ORDER BY id_client_type DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_client_type) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM client_type WHERE id_client_type = :id_client_type");
            $stmt->execute([':id_client_type' => $id_client_type]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO client_type (libelle) VALUES (:libelle)");
            $stmt->execute([
                ':libelle' => $libelle
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_client_type, $libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE client_type SET libelle = :libelle WHERE id_client_type = :id_client_type");
            $stmt->execute([
                ':libelle' => $libelle,
                ':id_client_type' => $id_client_type
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_client_type) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM client_type WHERE id_client_type = :id_client_type");
            $stmt->execute([':id_client_type' => $id_client_type]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
