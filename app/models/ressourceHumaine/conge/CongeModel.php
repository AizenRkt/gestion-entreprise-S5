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

    public function createDemandeConge($idEmploye, $idTypeConge, $dateDebut, $dateFin, $motif = '')
    {
        try {
            $db = Flight::db();

            // Calculer le nombre de jours (excluant les week-ends)
            $nbJours = $this->calculateWorkingDays($dateDebut, $dateFin);

            // Vérifier le solde disponible
            $soldeRestant = $this->getSoldeConge($idEmploye);
            if ($nbJours > $soldeRestant) {
                return ['success' => false, 'message' => 'Solde de congés insuffisant'];
            }

            // Insérer la demande
            $sql = "INSERT INTO demande_conge (id_employe, id_type_conge, date_debut, date_fin, nb_jours, motif) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                $idEmploye,
                $idTypeConge,
                $dateDebut,
                $dateFin,
                $nbJours,
                $motif
            ]);

            if ($success) {
                // Mettre à jour l'historique des congés
                $this->updateHistoriqueConge($idEmploye, $stmt->lastInsertId(), $nbJours);

                return ['success' => true, 'message' => 'Demande créée avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la création de la demande'];
            }

        } catch (\PDOException $e) {
            error_log("Erreur création demande congé: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique'];
        }
    }

    private function calculateWorkingDays($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $end->modify('+1 day'); // Inclure le dernier jour

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end);

        $workingDays = 0;
        foreach ($period as $date) {
            // Exclure les samedis (6) et dimanches (7)
            if ($date->format('N') < 6) {
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
}
