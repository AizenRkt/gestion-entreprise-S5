<?php

namespace app\models\ressourceHumaine\heureSupp;

use Flight;
use PDO;

class HeureSuppModel
{
    /**
     * Récupère tous les détails des heures supplémentaires depuis la vue.
     * @return array
     */
    public function getAllHeureSuppDetails(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_heure_sup_details";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Gérer l'erreur, par exemple en loggant ou en retournant un tableau vide
            error_log($e->getMessage());
            return [];
        }
    }
}
