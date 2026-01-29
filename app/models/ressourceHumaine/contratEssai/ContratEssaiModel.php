<?php
namespace app\models\ressourceHumaine\contratEssai;

use Flight;
use PDO;

class ContratEssaiModel {
    // Récupérer tous les contrats d'essai

    public function insert($id_candidat, $debut, $fin, $path) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO contrat_essai (id_candidat, debut, fin, pathPdf) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_candidat, $debut, $fin, $path]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_essai ORDER BY debut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAllDetail() {
        try {
            $db = Flight::db();

            $sql = "
                SELECT 
                    ce.id_contrat_essai,
                    ce.id_candidat,
                    ce.debut,
                    ce.fin,
                    ce.pathPdf,
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    e.email,
                    e.telephone,
                    e.genre,
                    e.date_embauche,

                    ces.statut AS statut,

                    cer.nouvelle_date_fin AS date_fin_renouvellement,
                    cer.commentaire AS commentaire_renouvellement

                FROM contrat_essai ce

                JOIN employe e 
                    ON ce.id_candidat = e.id_candidat

                LEFT JOIN contrat_essai_statut ces 
                    ON ces.id_contrat_essai = ce.id_contrat_essai
                    AND ces.id_statut_contrat_essai = (
                        SELECT id_statut_contrat_essai
                        FROM contrat_essai_statut
                        WHERE id_contrat_essai = ce.id_contrat_essai
                        ORDER BY date_statut DESC, id_statut_contrat_essai DESC
                        LIMIT 1
                    )

                LEFT JOIN contrat_essai_renouvellement cer 
                    ON cer.id_contrat_essai = ce.id_contrat_essai
                    AND cer.id_renouvellement_essai = (
                        SELECT id_renouvellement_essai
                        FROM contrat_essai_renouvellement
                        WHERE id_contrat_essai = ce.id_contrat_essai
                        ORDER BY date_renouvellement DESC, id_renouvellement_essai DESC
                        LIMIT 1
                    )

                ORDER BY ce.debut DESC
            ";

            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return [];
        }
    }

    // Récupérer par id_candidat
    public function getByCandidat($id_candidat) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_essai WHERE id_candidat = ?");
            $stmt->execute([$id_candidat]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Retourne tous les id_candidat sous contrat
    public function getAllCandidatIds() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT DISTINCT id_candidat FROM contrat_essai");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return [];
        }
    }

    // retourne seulement les contrats déjà validés
    public function getAllValider() {
        try {
            $db = Flight::db();

            $sql = "
                SELECT 
                    ce.id_contrat_essai,
                    ce.id_candidat,
                    ce.debut,
                    ce.fin,
                    ce.pathPdf,
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    e.email,
                    e.telephone,
                    e.genre,
                    e.date_embauche,

                    ces.statut AS statut,

                    cer.nouvelle_date_fin AS date_fin_renouvellement,
                    cer.commentaire AS commentaire_renouvellement

                FROM contrat_essai ce

                JOIN employe e 
                    ON ce.id_candidat = e.id_candidat

                LEFT JOIN contrat_essai_statut ces 
                    ON ces.id_contrat_essai = ce.id_contrat_essai
                    AND ces.id_statut_contrat_essai = (
                        SELECT id_statut_contrat_essai
                        FROM contrat_essai_statut
                        WHERE id_contrat_essai = ce.id_contrat_essai
                        ORDER BY date_statut DESC, id_statut_contrat_essai DESC
                        LIMIT 1
                    )

                LEFT JOIN contrat_essai_renouvellement cer 
                    ON cer.id_contrat_essai = ce.id_contrat_essai
                    AND cer.id_renouvellement_essai = (
                        SELECT id_renouvellement_essai
                        FROM contrat_essai_renouvellement
                        WHERE id_contrat_essai = ce.id_contrat_essai
                        ORDER BY date_renouvellement DESC, id_renouvellement_essai DESC
                        LIMIT 1
                    )

                WHERE ces.statut = 'Valider'
                AND NOT EXISTS (
                    SELECT 1
                    FROM contrat_travail ct
                    WHERE ct.id_employe = e.id_employe
                )
                ORDER BY ce.debut DESC
            ";

            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return [];
        }
    }

}
