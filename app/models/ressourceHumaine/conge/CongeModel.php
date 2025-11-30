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
        $taken = isset($takenRow['taken']) ? (int)$takenRow['taken'] : 0;

        $balance = $accrued - $taken;
        if ($balance < 0) $balance = 0.0;

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
}
