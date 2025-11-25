<?php

namespace app\models\ressourceHumaine\absence;

use Flight;
use PDO;
use DateTime;
use DateInterval;

class AbsenceModel
{
    /**
     * Récupère tous les détails des absences depuis la vue.
     * @return array
     */
    public function getAllAbsenceDetails(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_absence_details";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Gérer l'erreur, par exemple en loggant ou en retournant un tableau vide
            error_log($e->getMessage());
            return [];
        }
    }
}