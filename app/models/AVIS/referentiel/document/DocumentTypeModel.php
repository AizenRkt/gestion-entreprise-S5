<?php
namespace app\models\AVIS\referentiel\document;
use Flight;
use PDO;
use Exception;

class DocumentTypeModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM document_type_avis ORDER BY id_document_type DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_document_type) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM document_type_avis WHERE id_document_type = :id_document_type");
            $stmt->execute([':id_document_type' => $id_document_type]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO document_type_avis (libelle) VALUES (:libelle)");
            $stmt->execute([
                ':libelle' => $libelle
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_document_type, $libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE document_type_avis SET libelle = :libelle WHERE id_document_type = :id_document_type");
            $stmt->execute([
                ':libelle' => $libelle,
                ':id_document_type' => $id_document_type
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_document_type) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM document_type_avis WHERE id_document_type = :id_document_type");
            $stmt->execute([':id_document_type' => $id_document_type]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
