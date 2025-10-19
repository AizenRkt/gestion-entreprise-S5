<?php
namespace app\models\ressourceHumaine\scoring;
use Flight;
use PDO;
use Exception;
use PDOException;

class ScoringModel {
    public static function insertScore($id_candidat, $id_typeScoring, $score, $id_item) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO scoring (id_candidat, id_type_scoring, valeur, id_item)
                VALUES (:id_candidat, :id_type_scoring, :valeur, :id_item)
            ");
            $stmt->execute([
                ':id_candidat'     => $id_candidat,
                ':id_type_scoring' => $id_typeScoring,
                ':valeur'          => $score,
                ':id_item'         => $id_item
            ]);

            return $db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'insertion du score : " . $e->getMessage());
        }
    }

    public static function getCandidatScore($id_candidat) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT s.id_scoring, s.valeur, ts.id_type_scoring, ts.nom AS type_nom
                FROM scoring s
                INNER JOIN type_scoring ts ON s.id_type_scoring = ts.id_type_scoring
                WHERE s.id_candidat = :id_candidat
            ");
            $stmt->execute([':id_candidat' => $id_candidat]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des scores : " . $e->getMessage());
        }
    }

    public static function getEligibleEssaie($id_annonce) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare ("
                    SELECT DISTINCT 
                        c.id_candidat, 
                        c.nom, 
                        c.prenom, 
                        c.email, 
                        c.date_candidature,
                        sq.valeur AS note_qcm,
                        MAX(dq.bareme_question) AS note_max_qcm,
                        se.valeur AS note_entretien,
                        de.evaluation AS evaluation_entretien
                    FROM candidat c
                    JOIN cv ON cv.id_candidat = c.id_candidat
                    JOIN postulance p ON p.id_cv = cv.id_cv
                    -- Scores
                    JOIN scoring sq ON sq.id_candidat = c.id_candidat AND sq.id_type_scoring = 1
                    JOIN scoring se ON se.id_candidat = c.id_candidat AND se.id_type_scoring = 2
                    -- QCM détail pour récupérer la note max
                    JOIN detail_qcm dq ON dq.id_question = sq.id_item
                    -- Entretien
                    JOIN entretien_candidat ec ON ec.id_candidat = c.id_candidat
                    JOIN detail_entretien de ON de.id_entretien = ec.id_entretien
                    WHERE p.id_annonce = :id_annonce
                    AND sq.valeur >= dq.bareme_question / 2
                    AND de.evaluation = 'recommande'
                    GROUP BY c.id_candidat, c.nom, c.prenom, c.email, c.date_candidature, sq.valeur, se.valeur, de.evaluation;
                ");
            $stmt->execute([':id_annonce' => $id_annonce]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des scores : " . $e->getMessage());
        }
    }

    public static function getCandidatNonEligible() {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT id_candidat FROM contrat_essai
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des candidats non éligibles : " . $e->getMessage());
        }
    }
}
