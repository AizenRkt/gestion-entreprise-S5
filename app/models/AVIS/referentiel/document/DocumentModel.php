<?php
namespace app\models\AVIS\referentiel\document;
use Flight;
use PDO;
use Exception;

class DocumentModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT d.*, dt.libelle AS type_libelle 
                FROM document_avis d 
                JOIN document_type_avis dt ON d.id_document_type = dt.id_document_type 
                ORDER BY d.date_creation DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_document) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT d.*, dt.libelle AS type_libelle 
                FROM document_avis d 
                JOIN document_type_avis dt ON d.id_document_type = dt.id_document_type 
                WHERE d.id_document = :id_document
            ");
            $stmt->execute([':id_document' => $id_document]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByReference($reference) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT d.*, dt.libelle AS type_libelle 
                FROM document_avis d 
                JOIN document_type_avis dt ON d.id_document_type = dt.id_document_type 
                WHERE d.reference = :reference
            ");
            $stmt->execute([':reference' => $reference]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($id_document_type, $reference, $description = null, $path = null, $date_creation = null) {
        try {
            $db = Flight::db();
            if ($date_creation === null) {
                $date_creation = date('Y-m-d H:i:s');
            }
            $stmt = $db->prepare("
                INSERT INTO document_avis (id_document_type, reference, description, date_creation, path) 
                VALUES (:id_document_type, :reference, :description, :date_creation, :path)
            ");
            $stmt->execute([
                ':id_document_type' => $id_document_type,
                ':reference' => $reference,
                ':description' => $description,
                ':date_creation' => $date_creation,
                ':path' => $path
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_document, $id_document_type, $reference, $description = null, $path = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE document_avis 
                SET id_document_type = :id_document_type, reference = :reference, description = :description, path = :path 
                WHERE id_document = :id_document
            ");
            $stmt->execute([
                ':id_document_type' => $id_document_type,
                ':reference' => $reference,
                ':description' => $description,
                ':path' => $path,
                ':id_document' => $id_document
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_document) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM document_avis WHERE id_document = :id_document");
            $stmt->execute([':id_document' => $id_document]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function getByType($id_document_type) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT d.*, dt.libelle AS type_libelle 
                FROM document_avis d 
                JOIN document_type_avis dt ON d.id_document_type = dt.id_document_type 
                WHERE d.id_document_type = :id_document_type
                ORDER BY d.date_creation DESC
            ");
            $stmt->execute([':id_document_type' => $id_document_type]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function search($search_term) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT d.*, dt.libelle AS type_libelle 
                FROM document_avis d 
                JOIN document_type_avis dt ON d.id_document_type = dt.id_document_type 
                WHERE d.reference LIKE :search_term 
                OR d.description LIKE :search_term
                ORDER BY d.date_creation DESC
            ");
            $search_pattern = '%' . $search_term . '%';
            $stmt->execute([':search_term' => $search_pattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
