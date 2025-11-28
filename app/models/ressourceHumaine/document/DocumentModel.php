<?php
namespace app\models\ressourceHumaine\document;

use Flight;
use PDO;

class DocumentModel {

    public static function insertDocument($id_type_document, $id_employe, $titre, $pathScan = null, $date_expiration = null)
    {
        try {
            $db = Flight::db();

            $stmt = $db->prepare("
                INSERT INTO document (
                    id_type_document, id_employe, titre, pathScan, dateUpload, date_expiration
                ) VALUES (?, ?, ?, ?, CURRENT_DATE, ?)
            ");

            $stmt->execute([
                $id_type_document,
                $id_employe,
                $titre,
                $pathScan,
                $date_expiration
            ]);

            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function insertStatut($id_document, $statut, $commentaire = null)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO document_statut (id_document, statut, date_statut, commentaire)
                VALUES (?, ?, CURRENT_DATE, ?)
            ");

            return $stmt->execute([$id_document, $statut, $commentaire]);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getEmployeDocuments($id_employe)
    {
        try {
            $db = Flight::db();

            $stmt = $db->prepare("
                SELECT 
                    d.id_document,
                    d.titre,
                    d.pathScan,
                    d.dateUpload,
                    d.date_expiration,
                    dt.nom AS type_document,
                    dt.description,
                    ds.statut AS statut_actuel,
                    ds.date_statut AS date_statut,
                    ds.commentaire AS commentaire_statut
                FROM document d
                JOIN document_type dt ON d.id_type_document = dt.id_type_document
                LEFT JOIN document_statut ds ON ds.id_statut_document = (
                    SELECT id_statut_document 
                    FROM document_statut 
                    WHERE id_document = d.id_document
                    ORDER BY date_statut DESC
                    LIMIT 1
                )
                WHERE d.id_employe = ?
                ORDER BY d.dateUpload DESC
            ");

            $stmt->execute([$id_employe]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getAllDocuments()
    {
        try {
            $db = Flight::db();

            $stmt = $db->query("
                SELECT 
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    d.id_document,
                    d.titre,
                    d.pathScan,
                    d.dateUpload,
                    d.date_expiration,
                    dt.nom AS type_document,
                    ds.statut AS statut_actuel,
                    ds.date_statut
                FROM document d
                JOIN employe e ON d.id_employe = e.id_employe
                JOIN document_type dt ON d.id_type_document = dt.id_type_document
                LEFT JOIN document_statut ds ON ds.id_statut_document = (
                    SELECT id_statut_document 
                    FROM document_statut 
                    WHERE id_document = d.id_document
                    ORDER BY date_statut DESC
                    LIMIT 1
                )
                ORDER BY d.dateUpload DESC
            ");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return null;
        }
    }
}
