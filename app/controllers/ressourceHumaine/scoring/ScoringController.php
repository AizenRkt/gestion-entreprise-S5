<?php

namespace app\controllers\ressourceHumaine\scoring;

use app\models\ressourceHumaine\scoring\ScoringModel;

use Exception;
use PDOException;
use PDO;

use Flight;

class ScoringController {
    public static function getEligibleEssaie($id_annonce)
    {
        try {
            $result = ScoringModel::getEligibleEssaie($id_annonce); 
            $non_eligibles = ScoringModel::getCandidatNonEligible(); 

            $non_eligibles_ids = array_column($non_eligibles, 'id_candidat');

            $eligible = array_filter($result, function($candidat) use ($non_eligibles_ids) {
                return !in_array($candidat['id_candidat'], $non_eligibles_ids);
            });

            $eligible = array_values($eligible);

            Flight::json([
                "success" => true,
                "data" => $eligible
            ]);
        } catch (PDOException $e) {
            Flight::json([
                "success" => false,
                "error" => "Erreur lors de la récupération des scores : " . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Flight::json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
?>
