<?php

namespace app\models\ressourceHumaine\absence;

use app\models\ressourceHumaine\conge\CongeModel;
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

    /**
     * Valide une absence, la convertit en congé et met à jour tous les enregistrements associés.
     * @param int $id_absence
     * @param string $date_validation
     * @return array ['success' => bool, 'message' => string]
     */
    public function validerEtConvertirEnConge(int $id_absence, string $date_validation): array
    {
        $db = Flight::db();
        try {
            $db->beginTransaction();

            // 1. Get absence details
            $absence = $this->getAbsenceById($id_absence);
            if (!$absence) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Absence introuvable.'];
            }

            // 2. Server-side check of leave balance
            $congeModel = new CongeModel();
            $solde = $congeModel->calculateSoldeConge((int)$absence['id_employe'], $absence['date_fin'], $absence['date_debut']);
            
            $d1 = new \DateTime($absence['date_debut']);
            $d2 = new \DateTime($absence['date_fin']);
            $days = $d1->diff($d2)->days + 1;

            if ($solde['balance'] < $days) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Solde de congé insuffisant. Vérification côté serveur.'];
            }

            // 3. Create a new demande_conge (assuming type_conge 1 = Congé payé)
            $stmt_insert_conge = $db->prepare(
                "INSERT INTO demande_conge (id_type_conge, id_employe, date_debut, date_fin, nb_jours)
                 VALUES (1, :id_employe, :date_debut, :date_fin, :nb_jours)"
            );
            $stmt_insert_conge->execute([
                'id_employe' => $absence['id_employe'],
                'date_debut' => $absence['date_debut'],
                'date_fin' => $absence['date_fin'],
                'nb_jours' => $days
            ]);
            $id_demande_conge = $db->lastInsertId();

            // 4. Validate the new leave request
            $stmt_validate_conge = $db->prepare(
                "INSERT INTO validation_conge (id_demande_conge, statut, date_validation)
                 VALUES (:id_demande_conge, 'valide', :date_validation)"
            );
            $stmt_validate_conge->execute([
                'id_demande_conge' => $id_demande_conge,
                'date_validation' => $date_validation
            ]);

            // 5. Update pointage status to 'Congé'
            $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();
            $pointageModel->updatePointageStatusForDateRange(
                $absence['id_employe'],
                $absence['date_debut'],
                $absence['date_fin'],
                'Congé'
            );

            // 6. Delete the original absence and its documentation links
            // Find corresponding documentation
            $stmt_doc = $db->prepare("SELECT id_documentation_absence FROM documentation_absence WHERE id_employe = :id_employe AND date_debut = :date_debut AND date_fin = :date_fin LIMIT 1");
            $stmt_doc->execute([
                'id_employe' => $absence['id_employe'],
                'date_debut' => $absence['date_debut'],
                'date_fin' => $absence['date_fin']
            ]);
            $documentation = $stmt_doc->fetch(PDO::FETCH_ASSOC);

            if ($documentation) {
                 $stmt_delete_validation = $db->prepare("DELETE FROM validation_documentation_absence WHERE id_absence = :id_absence");
                 $stmt_delete_validation->execute(['id_absence' => $id_absence]);
            }
            
            $stmt_delete_absence = $db->prepare("DELETE FROM absence WHERE id_absence = :id_absence");
            $stmt_delete_absence->execute(['id_absence' => $id_absence]);

            $db->commit();
            return ['success' => true, 'message' => 'Absence validée et convertie en congé avec succès.'];

        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Une erreur de base de données est survenue.'];
        } catch (\Exception $e) {
             if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Une erreur inattendue est survenue.'];
        }
    }

    public function getAbsenceById(int $id_absence)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM absence WHERE id_absence = ? LIMIT 1");
            $stmt->execute([$id_absence]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
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
    /**
     * Récupère toutes les absences d'un employé pour un mois et une année donnés.
     *
     * @param int $id_employe L'identifiant de l'employé.
     * @param int $mois Le mois à filtrer (1-12).
     * @param int $annee L'année à filtrer.
     * @return array
     */
    public function getAllAbsencesByEmployeAndDate(int $id_employe, int $mois, int $annee): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_total_absences 
                    WHERE id_employe = :id_employe 
                    AND mois = :mois 
                    AND annee = :annee";
                    
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id_employe' => $id_employe,
                'mois' => $mois,
                'annee' => $annee
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Retourne tous les résultats
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];  // En cas d'erreur, retourne un tableau vide
        }
    }
}