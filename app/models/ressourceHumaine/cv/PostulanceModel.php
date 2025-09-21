<?php
namespace app\models\ressourceHumaine\cv;

use Flight;
use PDO;
use Exception;

class PostulanceModel {

    public static function insertPostulance($id_cv, $id_annonce) {
        $db = Flight::db();

        try {
            $stmt = $db->prepare("
                INSERT INTO postulance (id_cv, id_annonce, date_postulation)
                VALUES (?, ?, CURRENT_DATE)
            ");
            $stmt->execute([$id_cv, $id_annonce]);

            return $db->lastInsertId();

        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'insertion de la postulance : " . $e->getMessage());
        }
    }

    public static function exists($id_cv, $id_annonce) {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM postulance 
            WHERE id_cv = ? AND id_annonce = ?
        ");
        $stmt->execute([$id_cv, $id_annonce]);
        return $stmt->fetchColumn() > 0;
    }

    public static function eligibiliteBefore($id_annonce, $id_candidat) {
        $db = Flight::db();

        try {
            $sql = "
                SELECT c.id_candidat, c.nom, c.prenom, c.email, cv.id_cv
                FROM postulance p
                JOIN cv cv ON p.id_cv = cv.id_cv
                JOIN candidat c ON cv.id_candidat = c.id_candidat
                JOIN annonce a ON p.id_annonce = a.id_annonce
                WHERE p.id_annonce = :id_annonce
                AND (a.age_min IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) >= a.age_min)
                AND (a.age_max IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) <= a.age_max)
                AND NOT EXISTS (
                    SELECT 1
                    FROM detail_annonce da
                    WHERE da.id_annonce = a.id_annonce
                        AND da.type = 'competence'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM detail_cv dc
                            WHERE dc.id_cv = cv.id_cv
                            AND dc.type = 'competence'
                            AND dc.id_item = da.id_item
                        )
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM detail_annonce da
                    WHERE da.id_annonce = a.id_annonce
                        AND da.type = 'diplome'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM detail_cv dc
                            WHERE dc.id_cv = cv.id_cv
                            AND dc.type = 'diplome'
                            AND dc.id_item = da.id_item
                        )
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM detail_annonce da
                    WHERE da.id_annonce = a.id_annonce
                        AND da.type = 'ville'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM detail_cv dc
                            WHERE dc.id_cv = cv.id_cv
                            AND dc.type = 'ville'
                            AND dc.id_item = da.id_item
                        )
                )
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id_annonce' => $id_annonce]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des candidats éligibles : " . $e->getMessage());
            return []; 
        }
    }

    public static function eligibilite($id_annonce, $id_candidat) {
        $db = Flight::db();

        try {
            $sql = "
                SELECT 1
                FROM postulance p
                JOIN cv cv ON p.id_cv = cv.id_cv
                JOIN candidat c ON cv.id_candidat = c.id_candidat
                JOIN annonce a ON p.id_annonce = a.id_annonce
                WHERE p.id_annonce = :id_annonce
                AND c.id_candidat = :id_candidat
                AND (a.age_min IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) >= a.age_min)
                AND (a.age_max IS NULL OR TIMESTAMPDIFF(YEAR, c.date_naissance, CURDATE()) <= a.age_max)
                AND NOT EXISTS (
                    SELECT 1
                    FROM detail_annonce da
                    WHERE da.id_annonce = a.id_annonce
                        AND da.type = 'competence'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM detail_cv dc
                            WHERE dc.id_cv = cv.id_cv
                            AND dc.type = 'competence'
                            AND dc.id_item = da.id_item
                        )
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM detail_annonce da
                    WHERE da.id_annonce = a.id_annonce
                        AND da.type = 'diplome'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM detail_cv dc
                            WHERE dc.id_cv = cv.id_cv
                            AND dc.type = 'diplome'
                            AND dc.id_item = da.id_item
                        )
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM detail_annonce da
                    WHERE da.id_annonce = a.id_annonce
                        AND da.type = 'ville'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM detail_cv dc
                            WHERE dc.id_cv = cv.id_cv
                            AND dc.type = 'ville'
                            AND dc.id_item = da.id_item
                        )
                )
                LIMIT 1
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_annonce' => $id_annonce,
                ':id_candidat' => $id_candidat
            ]);

            // Retourne true si le candidat est trouvé, false sinon
            return $stmt->fetchColumn() !== false;

        } catch (\PDOException $e) {
            error_log("Erreur lors de la vérification de l'éligibilité : " . $e->getMessage());
            return false; 
        }
    }

}
