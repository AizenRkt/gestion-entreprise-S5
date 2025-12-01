<?php

namespace app\models\ressourceHumaine\conge;

use Flight;
use PDO;

class CongeModel
{
    /**
     * Récupère tous les détails des congés depuis la vue.
     * @return array
     */
    public function getAllCongeDetails(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_conge_details";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Gérer l'erreur, par exemple en loggant ou en retournant un tableau vide
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Récupère uniquement les congés validés pour le planning.
     * @return array
     */
    public function getValidatedConges(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_conge_details WHERE validation_statut = 'Validé'";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getAllTypesConge()
    {
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM type_conge WHERE nom != 'admin'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSoldeConge($idEmploye)
    {
        $db = Flight::db();
        $currentYear = date('Y');

        $stmt = $db->prepare("
            SELECT solde_restant 
            FROM solde_conge 
            WHERE id_employe = ? AND annee = ?
        ");
        $stmt->execute([$idEmploye, $currentYear]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['solde_restant'] : 0;
    }

    public function createDemandeConge($idEmploye, $idTypeConge, $dateDebut, $dateFin)
    {
        try {
            $db = Flight::db();

            // Calculer le nombre de jours (excluant les week-ends)
            $nbJours = $this->calculateWorkingDays($dateDebut, $dateFin);

            // Convertir les dates en format datetime (ajouter l'heure)
            $dateDebutDatetime = $dateDebut . ' 00:00:00';
            $dateFinDatetime = $dateFin . ' 23:59:59';

            // Debug: Afficher les données
            error_log("=== DEBUG DEMANDE CONGE ===");
            error_log("ID Employe: " . $idEmploye);
            error_log("ID Type Conge: " . $idTypeConge);
            error_log("Date Début: " . $dateDebutDatetime);
            error_log("Date Fin: " . $dateFinDatetime);
            error_log("Nb Jours: " . $nbJours);

            // Vérifier que le type de congé existe
            $stmtCheck = $db->prepare("SELECT COUNT(*) as count FROM type_conge WHERE id_type_conge = ?");
            $stmtCheck->execute([$idTypeConge]);
            $typeExists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($typeExists['count'] == 0) {
                error_log("ERREUR: Type de congé invalide: " . $idTypeConge);
                return ['success' => false, 'message' => 'Type de congé invalide'];
            }

            // Option 1: Insérer sans le motif (si la colonne n'existe pas)
            $sql = "INSERT INTO demande_conge (id_employe, id_type_conge, date_debut, date_fin, nb_jours) 
                    VALUES (?, ?, ?, ?, ?)";

            error_log("SQL: " . $sql);

            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                $idEmploye,
                $idTypeConge,
                $dateDebutDatetime,
                $dateFinDatetime,
                $nbJours
            ]);

            if ($success) {
                $lastInsertId = $db->lastInsertId();
                error_log("SUCCES: Demande créée avec ID: " . $lastInsertId);


                return ['success' => true, 'message' => 'Demande de congé soumise avec succès'];
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("ERREUR SQL: " . print_r($errorInfo, true));
                return ['success' => false, 'message' => 'Erreur lors de la création de la demande: ' . $errorInfo[2]];
            }

        } catch (\PDOException $e) {
            error_log("ERREUR PDO création demande congé: " . $e->getMessage());
            error_log("Code erreur: " . $e->getCode());
            return ['success' => false, 'message' => 'Erreur technique: ' . $e->getMessage()];
        }
    }
    public function canRequestConge($idEmploye)
    {
        $db = Flight::db();

        $stmt = $db->prepare("
            SELECT date_embauche 
            FROM employe 
            WHERE id_employe = ?
        ");
        $stmt->execute([$idEmploye]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || empty($result['date_embauche'])) {
            return false;
        }

        $dateEmbauche = new \DateTime($result['date_embauche']);
        $today = new \DateTime();
        $interval = $dateEmbauche->diff($today);

        // Vérifier si au moins 1 an d'ancienneté
        return $interval->y >= 1;
    }

    public function calculateWorkingDays($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        // Pour inclure le dernier jour dans le calcul
        $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end);

        $workingDays = 0;
        foreach ($period as $date) {
            // Exclure les samedis (6) et dimanches (7)
            $dayOfWeek = $date->format('N');
            if ($dayOfWeek < 6) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    private function updateHistoriqueConge($idEmploye, $idDemandeConge, $nbJours)
    {
        $db = Flight::db();

        $sql = "INSERT INTO historique_conge (id_employe, id_demande_conge, type_mouvement, nb_jours, date_mouvement, motif) 
                VALUES (?, ?, 'prise', ?, CURDATE(), 'Demande de congé soumise')";

        $stmt = $db->prepare($sql);
        $stmt->execute([$idEmploye, $idDemandeConge, $nbJours]);
    }
    public function processValidation(int $id_demande_conge, string $statut, string $date_validation): bool
    {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            // Supprimer toute validation existante pour cette demande
            $stmt_delete = $db->prepare("DELETE FROM validation_conge WHERE id_demande_conge = :id_demande_conge");
            $stmt_delete->execute(['id_demande_conge' => $id_demande_conge]);

            // Insérer la nouvelle validation
            $stmt_insert = $db->prepare(
                "INSERT INTO validation_conge (id_demande_conge, statut, date_validation)
                VALUES (:id_demande_conge, :statut, :date_validation)"
            );
            $stmt_insert->execute([
                'id_demande_conge' => $id_demande_conge,
                'statut' => $statut,
                'date_validation' => $date_validation
            ]);

            // Si le congé est validé, mettre à jour le statut dans la table de pointage
            if ($statut === 'valide') {
                $stmt_conge = $db->prepare("SELECT id_employe, date_debut, date_fin FROM demande_conge WHERE id_demande_conge = :id_demande_conge");
                $stmt_conge->execute(['id_demande_conge' => $id_demande_conge]);
                $conge_details = $stmt_conge->fetch(PDO::FETCH_ASSOC);

                if ($conge_details) {
                    $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();
                    $pointageModel->updatePointageStatusForDateRange(
                        $conge_details['id_employe'],
                        $conge_details['date_debut'],
                        $conge_details['date_fin'],
                        'Congé'
                    );
                }
            }

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
     * Met à jour les dates d'un congé et ajuste les pointages correspondants.
     * @param int $id_demande_conge
     * @param string $newStartDate
     * @param string $newEndDate
     * @return bool
     */
    public function updateCongeDates(int $id_demande_conge, string $newStartDate, string $newEndDate): bool
    {
        $db = Flight::db();
        try {
            $db->beginTransaction();
    
            // 1. Récupérer les anciennes informations du congé
            $stmt_old = $db->prepare("SELECT id_employe, date_debut, date_fin FROM demande_conge WHERE id_demande_conge = ?");
            $stmt_old->execute([$id_demande_conge]);
            $old_conge = $stmt_old->fetch(PDO::FETCH_ASSOC);
    
            if (!$old_conge) {
                $db->rollBack();
                return false;
            }
            $id_employe = $old_conge['id_employe'];
    
            // 2. Mettre à jour la demande de congé avec les nouvelles dates
            $new_start_dt = new \DateTime($newStartDate);
            $new_end_dt = new \DateTime($newEndDate);
            $nb_jours = $new_start_dt->diff($new_end_dt)->days + 1;
    
            $stmt_update = $db->prepare("UPDATE demande_conge SET date_debut = ?, date_fin = ?, nb_jours = ? WHERE id_demande_conge = ?");
            $stmt_update->execute([$newStartDate, $newEndDate, $nb_jours, $id_demande_conge]);
    
            // 3. Mettre à jour les enregistrements de pointage
            $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();
            
            // Réinitialiser le statut pour l'ancienne période de congé
            $pointageModel->updatePointageStatusForDateRange($id_employe, $old_conge['date_debut'], $old_conge['date_fin'], 'Absent');
            // Appliquer le statut "Congé" pour la nouvelle période
            $pointageModel->updatePointageStatusForDateRange($id_employe, $newStartDate, $newEndDate, 'Congé');
    
            $db->commit();
            return true;
    
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Erreur lors de la mise à jour du congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un congé et réinitialise les pointages.
     * @param int $id_demande_conge
     * @return bool
     */
    public function deleteConge(int $id_demande_conge): bool
    {
        $db = Flight::db();
        try {
            $db->beginTransaction();

            // 1. Récupérer les infos du congé avant suppression
            $stmt_old = $db->prepare("SELECT id_employe, date_debut, date_fin FROM demande_conge WHERE id_demande_conge = ?");
            $stmt_old->execute([$id_demande_conge]);
            $old_conge = $stmt_old->fetch(PDO::FETCH_ASSOC);

            if ($old_conge) {
                // 2. Réinitialiser le pointage
                $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();
                $pointageModel->updatePointageStatusForDateRange($old_conge['id_employe'], $old_conge['date_debut'], $old_conge['date_fin'], 'Absent');
            }
            
            // 3. Supprimer la validation et la demande
            $stmt_del_val = $db->prepare("DELETE FROM validation_conge WHERE id_demande_conge = ?");
            $stmt_del_val->execute([$id_demande_conge]);

            $stmt_del_dem = $db->prepare("DELETE FROM demande_conge WHERE id_demande_conge = ?");
            $stmt_del_dem->execute([$id_demande_conge]);

            $db->commit();
            return true;

        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Erreur lors de la suppression du congé : " . $e->getMessage());
            return false;
        }
    }


    /**
     * Calcule le solde de congé pour un employé à une date donnée.
     * Logique:
     * - Accumulation: 2.5 jours par mois (30 jours/an)
     * - On ne peut cumuler que sur les 36 derniers mois (max 90 jours)
     * - Les congés pris sont comptés à partir des enregistrements `pointage` où `statut = 'Congé'`.
     *
     * @param int $id_employe
     * @param string|null $asOfDate format 'Y-m-d' ou null pour aujourd'hui
     * @return array ['accrued' => float, 'taken' => float, 'balance' => float, 'period_start' => string, 'period_months' => int]
     */
    public function calculateSoldeConge(int $id_employe, ?string $asOfDate = null, ?string $demandeStart = null): array
    {
        $db = Flight::db();

        $asOf = $asOfDate ? new \DateTime($asOfDate) : new \DateTime();

        // Récupérer la date d'embauche et la date d'activation la plus récente (employe_statut.date_modification)
        $stmt = $db->prepare("SELECT date_embauche FROM employe WHERE id_employe = ? LIMIT 1");
        $stmt->execute([$id_employe]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['date_embauche'])) {
            // Si date embauche inconnue, on considère aucun solde
            return ['accrued' => 0.0, 'taken' => 0.0, 'balance' => 0.0, 'period_start' => null, 'period_months' => 0, 'activation_date' => null];
        }

        $dateEmb = new \DateTime($row['date_embauche']);

        // Chercher la date de modification la plus récente où l'employé était actif (activite = 1)
        $stmt3 = $db->prepare(
            "SELECT date_modification FROM employe_statut WHERE id_employe = ? AND activite = 1 AND date_modification <= ? ORDER BY date_modification DESC LIMIT 1"
        );
        $stmt3->execute([$id_employe, $asOf->format('Y-m-d H:i:s')]);
        $actRow = $stmt3->fetch(PDO::FETCH_ASSOC);

        $activationDate = $actRow && !empty($actRow['date_modification']) ? new \DateTime($actRow['date_modification']) : $dateEmb;

        // La période de calcul doit respecter la règle suivante :
        // Si la date d'activation est antérieure à (date_fin_demande - 36 mois),
        // alors la période de début est (date_fin_demande - 36 mois).
        // Sinon, la période de début est la date d'activation.
        $maxPeriodStart = (clone $asOf)->modify('-36 months');
        $effectivePeriodStart = $activationDate > $maxPeriodStart ? $activationDate : $maxPeriodStart;

        // Nombre de mois dans la période considérée (entre effectivePeriodStart et asOf)
        $interval = $effectivePeriodStart->diff($asOf);
        $monthsConsidered = ($interval->y * 12) + $interval->m;

        if ($monthsConsidered <= 0) {
            return ['accrued' => 0.0, 'taken' => 0.0, 'balance' => 0.0, 'period_start' => null, 'period_end' => $asOf->format('Y-m-d'), 'period_months' => 0, 'activation_date' => $activationDate->format('Y-m-d')];
        }

        // Ne pas dépasser 36 mois même si la demande commence plus tôt
        if ($monthsConsidered > 36) {
            $monthsConsidered = 36;
            $effectivePeriodStart = (clone $asOf)->modify('-36 months');
        }

        $accrued = $monthsConsidered * 2.5; // jours

        $periodStart = $effectivePeriodStart->format('Y-m-d');

        // Compter les jours de congé pris (pointage.statut = 'Congé') entre la date de début effective
        // de la période (qui est max(activationDate, asOf-36months)) et la date de fin (asOf).
        // Cela suit la règle: si activation_date >= asOf-36months alors interval = activation_date..asOf,
        // sinon interval = (asOf-36months)..asOf.
        $takenStart = $periodStart;

        // Compter les jours de congé pris (pointage.statut = 'Congé') dans l'intervalle approprié
        $stmt2 = $db->prepare(
            "SELECT COUNT(*) as taken FROM pointage WHERE id_employe = ? AND statut = 'Congé' AND date_pointage BETWEEN ? AND ?"
        );
        $stmt2->execute([$id_employe, $takenStart, $asOf->format('Y-m-d')]);
        $takenRow = $stmt2->fetch(PDO::FETCH_ASSOC);
        $taken = isset($takenRow['taken']) ? (int) $takenRow['taken'] : 0;

        $balance = $accrued - $taken;
        if ($balance < 0)
            $balance = 0.0;

        return [
            'accrued' => round($accrued, 2),
            'taken' => $taken,
            'balance' => round($balance, 2),
            'period_start' => $periodStart,
            'period_end' => $asOf->format('Y-m-d'),
            'period_months' => $monthsConsidered,
            'activation_date' => $activationDate->format('Y-m-d')
        ];
    }
    /**
     * Récupère tous les congés d'un employé pour un mois et une année donnés.
     *
     * @param int $id_employe L'identifiant de l'employé.
     * @param int $mois Le mois à filtrer (1-12).
     * @param int $annee L'année à filtrer.
     * @return array
     */
    public function getAllCongeByEmployeAndDate(int $id_employe, int $mois, int $annee): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_total_conges 
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