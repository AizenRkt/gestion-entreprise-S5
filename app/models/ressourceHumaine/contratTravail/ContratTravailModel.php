<?php
namespace app\models\ressourceHumaine\contratTravail;

use Flight;
use PDO;

class ContratTravailModel {

    public function insert($id_type_contrat, $id_employe, $debut, $fin = null, $salaire_base = null, $date_signature = null, $id_poste = null, $pathPdf = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO contrat_travail 
                (id_type_contrat, id_employe, debut, fin, salaire_base, date_signature, id_poste, pathPdf)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_type_contrat, $id_employe, $debut, $fin, $salaire_base, $date_signature, $id_poste, $pathPdf]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_travail ORDER BY debut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_travail WHERE id_contrat_travail = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAllWithTypeAndEmploye() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT ct.*, ctt.titre AS type_contrat, e.nom, e.prenom, e.email
                FROM contrat_travail ct
                JOIN contrat_travail_type ctt ON ct.id_type_contrat = ctt.id_type_contrat
                JOIN employe e ON ct.id_employe = e.id_employe
                ORDER BY ct.debut DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAllDetail()
    {
        try {
            $sql = "
                SELECT 
                    ct.id_contrat_travail,
                    ct.debut,
                    ct.fin,
                    ct.salaire_base,
                    ct.date_signature,
                    ct.date_creation,
                    ct.pathPdf,
                    
                    -- Type de contrat
                    t.titre AS type_contrat,
                    t.duree_max,

                    -- Employé
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    e.email,
                    e.telephone,

                    -- Poste
                    p.titre AS poste,

                    -- Dernier renouvellement
                    r.id_renouvellement,
                    r.nouvelle_date_fin,
                    r.commentaire,
                    r.date_renouvellement,
                    r.pathPdf AS renouvellement_pdf

                FROM contrat_travail ct

                LEFT JOIN contrat_travail_type t 
                    ON t.id_type_contrat = ct.id_type_contrat

                LEFT JOIN employe e 
                    ON e.id_employe = ct.id_employe

                LEFT JOIN poste p 
                    ON p.id_poste = ct.id_poste

                LEFT JOIN contrat_travail_renouvellement r 
                    ON r.id_renouvellement = (
                        SELECT r2.id_renouvellement 
                        FROM contrat_travail_renouvellement r2
                        WHERE r2.id_contrat_travail = ct.id_contrat_travail
                        ORDER BY r2.date_renouvellement DESC
                        LIMIT 1
                    )

                ORDER BY ct.id_contrat_travail DESC
            ";

            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;

        } catch (\Exception $e) {
            error_log("Erreur getAllDetail: " . $e->getMessage());
            return false;
        }
    }

    public function getByIdDetail($id)
    {
        try {
            $sql = "SELECT ct.*, t.titre AS type_titre, t.max_nb_renouvellement, t.max_duree_renouvellement
                    FROM contrat_travail ct
                    LEFT JOIN contrat_travail_type t ON t.id_type_contrat = ct.id_type_contrat
                    WHERE ct.id_contrat_travail = :id
                    LIMIT 1";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log("ContratTravailModel::getById error: " . $e->getMessage());
            return null;
        }
    }

    public function compteRenouvellement($id_contrat_travail)
    {
        try {
            $sql = "SELECT COUNT(*) as nb FROM contrat_travail_renouvellement WHERE id_contrat_travail = :id";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([':id' => $id_contrat_travail]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($r['nb'] ?? 0);
        } catch (\Exception $e) {
            error_log("ContratTravailModel::compteRenouvellement error: " . $e->getMessage());
            return false;
        }
    }

    public function InsertRenouvellement($id_contrat_travail, $nouvelle_date_fin, $commentaire, $date_renouvellement, $pathPdf = null)
    {
        try {
            $sql = "INSERT INTO contrat_travail_renouvellement 
                    (id_contrat_travail, nouvelle_date_fin, commentaire, date_renouvellement, date_creation, pathPdf)
                    VALUES (:id_contrat_travail, :nouvelle_date_fin, :commentaire, :date_renouvellement, :date_creation, :pathPdf)";
            $stmt = Flight::db()->prepare($sql);
            $nowDate = date('Y-m-d');
            $stmt->execute([
                ':id_contrat_travail' => $id_contrat_travail,
                ':nouvelle_date_fin' => $nouvelle_date_fin,
                ':commentaire' => $commentaire,
                ':date_renouvellement' => $date_renouvellement,
                ':date_creation' => $nowDate,
                ':pathPdf' => $pathPdf
            ]);
            return Flight::db()->lastInsertId();
        } catch (\Exception $e) {
            error_log("erreur de renouvellement: " . $e->getMessage());
            return false;
        }
    }

    public function compteMigrationCDDtoCDI($id_cdd)
    {
        try {
            $sql = "SELECT COUNT(*) as nb FROM contrat_migration_cdd_cdi WHERE id_cdd = :id";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([':id' => $id_cdd]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($r['nb'] ?? 0);
        } catch (\Exception $e) {
            error_log("ContratTravailModel::compteRenouvellement error: " . $e->getMessage());
            return false;
        }
    }
}
