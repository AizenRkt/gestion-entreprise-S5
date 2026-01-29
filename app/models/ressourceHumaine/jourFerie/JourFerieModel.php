<?php

namespace app\models\ressourceHumaine\jourFerie;

use Flight;
use PDO;

class JourFerieModel
{
    /**
     * Crée un nouveau jour férié.
     * @param string $date
     * @param string $description
     * @param string $recurrence
     * @return int|false L'ID du jour férié inséré ou false en cas d'échec.
     */
    public function createJourFerie($date, $description, $recurrence)
    {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO jour_ferie (date, description, recurrence) VALUES (?, ?, ?)");
        if ($stmt->execute([$date, $description, $recurrence])) {
            return $db->lastInsertId();
        }
        return false;
    }

    /**
     * Récupère tous les jours fériés.
     * @return array
     */
    public function getAllJoursFeries()
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM jour_ferie ORDER BY date ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un jour férié par son ID.
     * @param int $id
     * @return mixed
     */
    public function getJourFerieById($id)
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM jour_ferie WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour un jour férié.
     * @param int $id
     * @param string $date
     * @param string $description
     * @param string $recurrence
     * @return bool True en cas de succès, false sinon.
     */
    public function updateJourFerie($id, $date, $description, $recurrence)
    {
        $db = Flight::db();
        $stmt = $db->prepare("UPDATE jour_ferie SET date = ?, description = ?, recurrence = ? WHERE id = ?");
        return $stmt->execute([$date, $description, $recurrence, $id]);
    }

    /**
     * Supprime un jour férié.
     * @param int $id
     * @return bool True en cas de succès, false sinon.
     */
    public function deleteJourFerie($id)
    {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM jour_ferie WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
