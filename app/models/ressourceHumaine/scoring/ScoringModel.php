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

    public static function getMoyenneCandidat($id_candidat) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT AVG(valeur) AS moyenne
                FROM scoring
                WHERE id_candidat = :id_candidat
            ");
            $stmt->execute([':id_candidat' => $id_candidat]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? floatval($result['moyenne']) : null;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du calcul de la moyenne : " . $e->getMessage());
        }
    }

}
