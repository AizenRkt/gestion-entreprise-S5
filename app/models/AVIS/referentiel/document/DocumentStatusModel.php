<?php
namespace app\models\AVIS\referentiel\document;
use Flight;
use PDO;
use Exception;

class DocumentStatusModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM document_status_avis ORDER BY id_document_status DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_document_status) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM document_status_avis WHERE id_document_status = :id_document_status");
            $stmt->execute([':id_document_status' => $id_document_status]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO document_status_avis (libelle) VALUES (:libelle)");
            $stmt->execute([
                ':libelle' => $libelle
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_document_status, $libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE document_status_avis SET libelle = :libelle WHERE id_document_status = :id_document_status");
            $stmt->execute([
                ':libelle' => $libelle,
                ':id_document_status' => $id_document_status
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_document_status) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM document_status_avis WHERE id_document_status = :id_document_status");
            $stmt->execute([':id_document_status' => $id_document_status]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
