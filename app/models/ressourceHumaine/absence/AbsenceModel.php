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

    public function validerAbsence(int $id_absence): bool
    {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            // 1. Get absence details
            $stmt_absence = $db->prepare("SELECT id_employe, date_debut, date_fin FROM absence WHERE id_absence = :id_absence");
            $stmt_absence->execute(['id_absence' => $id_absence]);
            $absence_details = $stmt_absence->fetch(PDO::FETCH_ASSOC);

            if (!$absence_details) {
                $db->rollBack();
                return false;
            }

            // 2. Find corresponding documentation
            $stmt_doc = $db->prepare("SELECT id_documentation_absence FROM documentation_absence WHERE id_employe = :id_employe AND date_debut = :date_debut AND date_fin = :date_fin");
            $stmt_doc->execute([
                'id_employe' => $absence_details['id_employe'],
                'date_debut' => $absence_details['date_debut'],
                'date_fin' => $absence_details['date_fin']
            ]);
            $documentation = $stmt_doc->fetch(PDO::FETCH_ASSOC);

            if (!$documentation) {
                $db->rollBack();
                return false;
            }
            $id_documentation_absence = $documentation['id_documentation_absence'];
            
            // Check if already validated
            $stmt_check = $db->prepare("SELECT 1 FROM validation_documentation_absence WHERE id_absence = :id_absence AND id_documentation_absence = :id_documentation_absence");
            $stmt_check->execute([
                'id_absence' => $id_absence,
                'id_documentation_absence' => $id_documentation_absence
            ]);
            if ($stmt_check->fetch()) {
                $db->commit();
                return true; // Already validated
            }

            // 3. Insert into validation table
            $stmt_validation = $db->prepare("INSERT INTO validation_documentation_absence (id_documentation_absence, id_absence) VALUES (:id_documentation_absence, :id_absence)");
            $stmt_validation->execute([
                'id_documentation_absence' => $id_documentation_absence,
                'id_absence' => $id_absence
            ]);

            // 4. Update pointage status
            $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();
            $pointageModel->updatePointageStatusForDateRange(
                $absence_details['id_employe'],
                $absence_details['date_debut'],
                $absence_details['date_fin'],
                'Absence justifiée'
            );

            $db->commit();
            return true;
        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log($e->getMessage());
            return false;
        }
    }

    public function refuserAbsence(int $id_absence): bool
    {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            $stmt_delete_validation = $db->prepare("DELETE FROM validation_documentation_absence WHERE id_absence = :id_absence");
            $stmt_delete_validation->execute(['id_absence' => $id_absence]);

            $stmt_delete_absence = $db->prepare("DELETE FROM absence WHERE id_absence = :id_absence");
            $stmt_delete_absence->execute(['id_absence' => $id_absence]);

            $db->commit();
            return true;
        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log($e->getMessage());
            return false;
        }
    }
}