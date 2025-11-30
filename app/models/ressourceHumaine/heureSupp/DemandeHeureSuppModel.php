<?php

namespace app\models\ressourceHumaine\heureSupp;

use Flight;
use PDO;

class DemandeHeureSuppModel
{
    public function creerDemandeHeureSupp(array $data): array
    {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            // Valider les données
            $errors = $this->validerDonnees($data);
            if (!empty($errors)) {
                return ['success' => false, 'message' => implode(', ', $errors)];
            }

            // Récupérer l'ID de l'employé connecté
            $id_employe = $this->getEmployeConnecte();
            if (!$id_employe) {
                return ['success' => false, 'message' => 'Employé non identifié'];
            }

            // Vérifier les heures maximales
            if (!$this->verifierHeuresMax($data, $id_employe)) {
                return ['success' => false, 'message' => 'Dépassement du nombre maximum d\'heures supplémentaires autorisées'];
            }

            // 1. Créer la demande d'heures supplémentaires
            $stmt_demande = $db->prepare(
                "INSERT INTO demande_heure_sup (id_employe, date_demande) 
                 VALUES (:id_employe, NOW())"
            );
            
            $stmt_demande->execute([
                'id_employe' => $id_employe
            ]);
            
            $id_demande_heure_sup = $db->lastInsertId();

            // 2. Créer les détails des heures supplémentaires
            $stmt_detail = $db->prepare(
                "INSERT INTO detail_heure_sup (id_demande_heure_sup, heure_debut, heure_fin, date_debut, date_fin) 
                 VALUES (:id_demande_heure_sup, :heure_debut, :heure_fin, :date_debut, :date_fin)"
            );
            
            $stmt_detail->execute([
                'id_demande_heure_sup' => $id_demande_heure_sup,
                'heure_debut' => $data['heure_debut'],
                'heure_fin' => $data['heure_fin'],
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin']
            ]);

            $db->commit();
            return ['success' => true, 'message' => 'Demande d\'heures supplémentaires créée avec succès. En attente de validation.'];

        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Erreur dans creerDemandeHeureSupp: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création de la demande: ' . $e->getMessage()];
        }
    }

    private function validerDonnees(array $data): array
    {
        $errors = [];

        if (empty($data['heure_debut']) || empty($data['heure_fin']) || empty($data['date_debut']) || empty($data['date_fin'])) {
            $errors[] = 'Tous les champs obligatoires doivent être remplis';
        }

        if (!empty($data['heure_debut']) && !empty($data['heure_fin'])) {
            $heureDebut = strtotime($data['heure_debut']);
            $heureFin = strtotime($data['heure_fin']);
            
            if ($heureFin <= $heureDebut) {
                $errors[] = 'L\'heure de fin doit être après l\'heure de début';
            }

            // Vérifier que les heures sont dans des plages raisonnables (ex: 18h-22h)
            $heureDebutObj = \DateTime::createFromFormat('H:i', $data['heure_debut']);
            $heureFinObj = \DateTime::createFromFormat('H:i', $data['heure_fin']);
            
            if ($heureDebutObj && $heureFinObj) {
                $heureDebutInt = (int)$heureDebutObj->format('H');
                $heureFinInt = (int)$heureFinObj->format('H');
                
                if ($heureDebutInt < 17 || $heureFinInt > 23) {
                    $errors[] = 'Les heures supplémentaires doivent être entre 17h et 23h';
                }
            }
        }

        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            $dateDebut = new \DateTime($data['date_debut']);
            $dateFin = new \DateTime($data['date_fin']);
            
            if ($dateFin < $dateDebut) {
                $errors[] = 'La date de fin doit être après la date de début';
            }
        }

        return $errors;
    }

    private function verifierHeuresMax(array $data, int $id_employe): bool
    {
        try {
            $db = Flight::db();
            
            // Calculer le nombre d'heures demandées
            $heureDebut = strtotime($data['heure_debut']);
            $heureFin = strtotime($data['heure_fin']);
            $heuresDemandees = ($heureFin - $heureDebut) / 3600; // Conversion en heures

            // Récupérer le maximum d'heures autorisées
            $stmt_max = $db->prepare(
                "SELECT nb_heures_max_par_semaine 
                 FROM max_heure_sup 
                 WHERE date_application <= CURDATE() 
                 ORDER BY date_application DESC 
                 LIMIT 1"
            );
            $stmt_max->execute();
            $max_heures = $stmt_max->fetch(PDO::FETCH_ASSOC);

            if (!$max_heures) {
                return true; // Pas de limite définie
            }

            $heuresMax = $max_heures['nb_heures_max_par_semaine'];

            // Calculer les heures déjà validées cette semaine
            $dateDemande = new \DateTime($data['date_debut']);
            $debutSemaine = $dateDemande->modify('this week')->format('Y-m-d');
            $finSemaine = $dateDemande->modify('this week +6 days')->format('Y-m-d');

            $stmt_heures_existantes = $db->prepare(
                "SELECT SUM(TIME_TO_SEC(TIMEDIFF(dh.heure_fin, dh.heure_debut))) / 3600 as total_heures
                 FROM detail_heure_sup dh
                 JOIN demande_heure_sup dhs ON dh.id_demande_heure_sup = dhs.id_demande_heure_sup
                 JOIN validation_heure_sup vhs ON dhs.id_demande_heure_sup = vhs.id_demande_heure_sup
                 WHERE dhs.id_employe = :id_employe
                 AND vhs.statut = 'valide'
                 AND dh.date_debut BETWEEN :debut_semaine AND :fin_semaine"
            );

            $stmt_heures_existantes->execute([
                'id_employe' => $id_employe,
                'debut_semaine' => $debutSemaine,
                'fin_semaine' => $finSemaine
            ]);

            $result = $stmt_heures_existantes->fetch(PDO::FETCH_ASSOC);
            $heuresExistantes = $result['total_heures'] ?? 0;

            return ($heuresExistantes + $heuresDemandees) <= $heuresMax;

        } catch (\PDOException $e) {
            error_log("Erreur dans verifierHeuresMax: " . $e->getMessage());
            return true; // En cas d'erreur, on laisse passer
        }
    }

    private function getEmployeConnecte()
    {
        // À adapter selon votre système d'authentification
        // Pour le moment, retourner un ID fixe pour tester
        return 1;
    }
}