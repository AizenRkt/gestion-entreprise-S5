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

            // 1. Récupérer les détails de l'absence
            $absence = $this->getAbsenceById($id_absence);
            if (!$absence) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Absence introuvable.'];
            }
            $id_employe = (int)$absence['id_employe'];
            $id_type_absence = (int)$absence['id_type_absence']; // Added
            $date_debut_absence = $absence['date_debut'];
            $date_fin_absence = $absence['date_fin'];

            // Récupérer le nom du type d'absence
            $stmt_type_absence = $db->prepare("SELECT nom FROM type_absence WHERE id_type_absence = :id_type_absence");
            $stmt_type_absence->execute(['id_type_absence' => $id_type_absence]);
            $type_absence_nom = $stmt_type_absence->fetchColumn(); // Added

            // 2. Valider la documentation associée en insérant l'enregistrement
            $stmt_doc = $db->prepare("SELECT id_documentation_absence FROM documentation_absence WHERE id_employe = :id_employe AND date_debut = :date_debut AND date_fin = :date_fin LIMIT 1");
            $stmt_doc->execute(['id_employe' => $id_employe, 'date_debut' => $date_debut_absence, 'date_fin' => $date_fin_absence]);
            $documentation = $stmt_doc->fetch(PDO::FETCH_ASSOC);

            if ($documentation) {
                // Insérer la validation pour marquer le document comme traité.
                // Cet enregistrement sera supprimé à la fin avec l'absence.
                $stmt_insert_validation = $db->prepare("INSERT INTO validation_documentation_absence (id_documentation_absence, id_absence) VALUES (:id_doc, :id_absence)");
                $stmt_insert_validation->execute(['id_doc' => $documentation['id_documentation_absence'], 'id_absence' => $id_absence]);
            }

            // 3. Calculer le solde et les jours ouvrés de l'absence
            $congeModel = new CongeModel();
            $jours_absence = $congeModel->calculateWorkingDays($date_debut_absence, $date_fin_absence);
            $solde = $congeModel->calculateSoldeConge($id_employe, $date_fin_absence, $date_debut_absence);
            $solde_disponible = $solde['balance'];

            // IDs et Statuts
            $ID_CONGE_PAYE = 1;
            $ID_CONGE_SANS_SOLDE = 2; // "Congé sans solde"
            $ID_CONGE_SPECIAUX = 3; // Using 'Congé maladie' ID for special leaves

            $STATUT_CONGE_PAYE = 'Congé';
            $STATUT_CONGE_NON_PAYE = 'Congé non payé';
            $STATUT_CONGE_SPECIAUX = 'Congé Spéciaux';
            $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();

            // 4. Logique de conversion et mise à jour du pointage
            $specialAbsenceTypes = ['Maladie', 'Congé Maternel', 'Congé Paternité'];

            if (in_array($type_absence_nom, $specialAbsenceTypes)) {
                // Pour les congés spéciaux, on ignore le solde et on met le statut "Congé Spéciaux"
                $this->creerEtValiderConge($id_employe, $ID_CONGE_SPECIAUX, $date_debut_absence, $date_fin_absence, $jours_absence, $date_validation);
                $pointageModel->updatePointageStatusForDateRange($id_employe, $date_debut_absence, $date_fin_absence, $STATUT_CONGE_SPECIAUX);
                $message = 'Absence de type "' . $type_absence_nom . '" convertie en congé spécial.';
            }
            elseif ($solde_disponible >= $jours_absence) {
                // Cas 1: Solde suffisant
                $this->creerEtValiderConge($id_employe, $ID_CONGE_PAYE, $date_debut_absence, $date_fin_absence, $jours_absence, $date_validation);
                $pointageModel->updatePointageStatusForDateRange($id_employe, $date_debut_absence, $date_fin_absence, $STATUT_CONGE_PAYE);
                $message = 'Absence entièrement convertie en congé.';

            }
            elseif ($solde_disponible > 0) {
                // Cas 2: Solde partiel
                $jours_payes = floor($solde_disponible);
                $jours_sans_solde = $jours_absence - $jours_payes;

                // Partie payée
                $date_fin_paye = $this->calculateEndDate($date_debut_absence, $jours_payes);
                $this->creerEtValiderConge($id_employe, $ID_CONGE_PAYE, $date_debut_absence, $date_fin_paye, $jours_payes, $date_validation);
                $pointageModel->updatePointageStatusForDateRange($id_employe, $date_debut_absence, $date_fin_paye, $STATUT_CONGE_PAYE);

                // Partie non payée
                $date_debut_sans_solde = new DateTime($date_fin_paye);
                $date_debut_sans_solde->modify('+1 day');
                while ($date_debut_sans_solde->format('N') >= 6) { $date_debut_sans_solde->modify('+1 day'); }

                $this->creerEtValiderConge($id_employe, $ID_CONGE_SANS_SOLDE, $date_debut_sans_solde->format('Y-m-d'), $date_fin_absence, $jours_sans_solde, $date_validation);
                $pointageModel->updatePointageStatusForDateRange($id_employe, $date_debut_sans_solde->format('Y-m-d'), $date_fin_absence, $STATUT_CONGE_NON_PAYE);
                
                $message = "Absence convertie en {$jours_payes} jour(s) de congé et {$jours_sans_solde} jour(s) de congé non payé.";

            } else {
                // Cas 3: Pas de solde
                $this->creerEtValiderConge($id_employe, $ID_CONGE_SANS_SOLDE, $date_debut_absence, $date_fin_absence, $jours_absence, $date_validation);
                $pointageModel->updatePointageStatusForDateRange($id_employe, $date_debut_absence, $date_fin_absence, $STATUT_CONGE_NON_PAYE);
                $message = 'Solde insuffisant. Absence entièrement convertie en congé non payé.';
            }

            // 5. Nettoyage : L'absence originale et sa validation sont conservées pour l'historique.
            // On ne supprime plus les enregistrements.
            /*
            $stmt_delete_validation = $db->prepare("DELETE FROM validation_documentation_absence WHERE id_absence = :id_absence");
            $stmt_delete_validation->execute(['id_absence' => $id_absence]);
            
            $stmt_delete_absence = $db->prepare("DELETE FROM absence WHERE id_absence = :id_absence");
            $stmt_delete_absence->execute(['id_absence' => $id_absence]);
            */

            $db->commit();
            return ['success' => true, 'message' => $message];

        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Une erreur inattendue est survenue: ' . $e->getMessage()];
        }
    }

    /**
     * Helper to create and validate a leave request within a transaction.
     */
    private function creerEtValiderConge($id_employe, $id_type_conge, $date_debut, $date_fin, $nb_jours, $date_validation)
    {
        $db = Flight::db();
        // Create leave request
        $stmt_insert_conge = $db->prepare(
            "INSERT INTO demande_conge (id_type_conge, id_employe, date_debut, date_fin, nb_jours)
             VALUES (:id_type_conge, :id_employe, :date_debut, :date_fin, :nb_jours)"
        );
        $stmt_insert_conge->execute([
            'id_type_conge' => $id_type_conge,
            'id_employe' => $id_employe,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'nb_jours' => $nb_jours
        ]);
        $id_demande_conge = $db->lastInsertId();

        // Validate the new leave request
        $stmt_validate_conge = $db->prepare(
            "INSERT INTO validation_conge (id_demande_conge, statut, date_validation)
             VALUES (:id_demande_conge, 'valide', :date_validation)"
        );
        $stmt_validate_conge->execute([
            'id_demande_conge' => $id_demande_conge,
            'date_validation' => $date_validation
        ]);
    }

    /**
     * Calculates the end date given a start date and a number of working days.
     */
    private function calculateEndDate(string $startDate, int $workingDays): string
    {
        $currentDate = new DateTime($startDate);
        $daysCounted = 0;
        while ($daysCounted < $workingDays) {
            // Sunday (7) and Saturday (6)
            if ($currentDate->format('N') < 6) {
                $daysCounted++;
            }
            if ($daysCounted < $workingDays) {
                $currentDate->modify('+1 day');
            }
        }
        return $currentDate->format('Y-m-d');
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