<?php

namespace app\controllers\ressourceHumaine\conge;

use app\models\ressourceHumaine\conge\CongeModel;
use Flight;
use PDO;

class CongeController
{
    private $congeModel;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
    }

    public function showCongePage()
    {
        $conges = $this->congeModel->getAllCongeDetails();
        Flight::render('ressourceHumaine/back/conge/conge', ['conges' => $conges]);
    }
    public function showDemandeForm()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user']['id_employe'])) {
            Flight::redirect('/log');
            return;
        }

        $model = new CongeModel();
        $typesConge = $model->getAllTypesConge();
        
        // Vérifier si l'employé a au moins 1 an d'ancienneté
        $canRequestConge = $model->canRequestConge($_SESSION['user']['id_employe']);

        Flight::render('ressourceHumaine/back/conge/demande', [
            'typesConge' => $typesConge,
            'canRequestConge' => $canRequestConge
        ]);
    }

    private function validateDemande($data)
    {
        $errors = [];

        // Vérification type de congé
        if (empty($data['id_type_conge'])) {
            $errors[] = "Le type de congé est obligatoire.";
        }

        // Vérification date début
        if (empty($data['date_debut'])) {
            $errors[] = "La date de début est obligatoire.";
        }

        // Vérification date fin
        if (empty($data['date_fin'])) {
            $errors[] = "La date de fin est obligatoire.";
        }

        // Vérification cohérence des dates
        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
                $errors[] = "La date de fin doit être après la date de début.";
            }
        }

        // Vérification délai de 15 jours
        if (!empty($data['date_debut'])) {
            $dateDebut = new \DateTime($data['date_debut']);
            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            $interval = $today->diff($dateDebut);
            
            if ($interval->days < 15 || $interval->invert == 1) {
                $errors[] = "La demande doit être faite au moins 15 jours avant la date de début.";
            }
        }

        return $errors;
    }

    public function submitDemande()
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user']['id_employe'])) {
            Flight::json(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $model = new CongeModel();
        
        // Vérifier si l'employé a au moins 1 an d'ancienneté
        if (!$model->canRequestConge($_SESSION['user']['id_employe'])) {
            Flight::json(['success' => false, 'message' => 'Vous ne pouvez pas faire de demande de congé pendant votre première année de contrat.']);
            return;
        }

        $data = Flight::request()->data;

        // Validation des données
        $errors = $this->validateDemande($data);
        if (!empty($errors)) {
            Flight::json(['success' => false, 'message' => 'Données invalides', 'errors' => $errors]);
            return;
        }

        $result = $model->createDemandeConge(
            $_SESSION['user']['id_employe'],
            $data['id_type_conge'],
            $data['date_debut'],
            $data['date_fin']
        );

        if ($result['success']) {
            Flight::json(['success' => true, 'message' => 'Demande de congé soumise avec succès']);
        } else {
            Flight::json(['success' => false, 'message' => $result['message']]);
        }
    }

    public function calculerJoursOuvrees()
    {
        $data = Flight::request()->data;
        $dateDebut = $data['date_debut'] ?? '';
        $dateFin = $data['date_fin'] ?? '';

        if (empty($dateDebut) || empty($dateFin)) {
            Flight::json(['success' => false, 'nbJours' => 0]);
            return;
        }

        $model = new CongeModel();
        $nbJours = $model->calculateWorkingDays($dateDebut, $dateFin);
        
        Flight::json(['success' => true, 'nbJours' => $nbJours]);
    }


    public function validerConge()
    {
        $data = Flight::request()->data;
        $id_demande = $data['id_demande_conge'];
        $date_validation = $data['date_validation'];

        $result = $this->congeModel->processValidation($id_demande, 'valide', $date_validation);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Demande de congé validée avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la validation.']);
        }
    }

    public function refuserConge()
    {
        $data = Flight::request()->data;
        $id_demande = $data['id_demande_conge'];
        $date_validation = $data['date_validation'];

        $result = $this->congeModel->processValidation($id_demande, 'refuse', $date_validation);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Demande de congé refusée avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors du refus.']);
        }
    }

    /**
     * API: retourne le solde de congé pour une demande donnée (utilisé avant validation)
     * GET param: id (id_demande_conge)
     */
    public function getSoldeForDemande()
    {
        $q = Flight::request()->query;
        $id = $q['id'] ?? null;
        if (!$id) {
            Flight::json(['success' => false, 'message' => 'id manquant'], 400);
            return;
        }

        $db = Flight::db();
        $stmt = $db->prepare("SELECT id_employe, date_debut, date_fin FROM demande_conge WHERE id_demande_conge = ? LIMIT 1");
        $stmt->execute([$id]);
        $demande = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$demande) {
            Flight::json(['success' => false, 'message' => 'Demande introuvable'], 404);
            return;
        }

        // calculer nombre de jours demandés (inclusif)
        try {
            $d1 = new \DateTime($demande['date_debut']);
            $d2 = new \DateTime($demande['date_fin']);
            $days = $d1->diff($d2)->days + 1;
        } catch (\Exception $e) {
            $days = 0;
        }

        // La période prise en compte doit se terminer à la date de fin de la demande
        // Passer date_debut + date_fin à la fonction pour que le calcul tienne compte de la période de la demande
        $solde = $this->congeModel->calculateSoldeConge((int)$demande['id_employe'], $demande['date_fin'], $demande['date_debut']);

        $canValidate = ($solde['balance'] >= $days);

        // Exposer la date_debut de la demande pour affichage clair dans la modal
        $solde['request_start'] = $demande['date_debut'];

        Flight::json(['success' => true, 'data' => ['solde' => $solde, 'days' => $days, 'canValidate' => $canValidate]]);
    }

    /**
     * API: retourne les congés validés pour l'affichage dans FullCalendar
     */
    public function getCongesForPlanning()
    {
        $conges = $this->congeModel->getValidatedConges();
        $events = [];
        foreach ($conges as $conge) {
            // Pour que la date de fin soit inclusive dans FullCalendar, il faut ajouter 1 jour.
            $dateFin = new \DateTime($conge['date_fin']);
            $dateFin->modify('+1 day');

            $color = $this->stringToColor($conge['id_employe']);
            
            $events[] = [
                'id'    => $conge['id_demande_conge'],
                'title' => $conge['employe_prenom'] . ' ' . $conge['employe_nom'],
                'start' => $conge['date_debut'],
                'end' => $dateFin->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => $conge['type_conge_nom']
                ]
            ];
        }
        Flight::json($events);
    }

    /**
     * API: met à jour les dates d'un congé suite à un drag-and-drop
     */
    public function updateCongeDate()
    {
        $data = Flight::request()->data;
        $id = $data['id_demande_conge'] ?? null;
        $start = $data['new_start'] ?? null;
        $end = $data['new_end'] ?? null;

        if (!$id || !$start || !$end) {
            Flight::json(['success' => false, 'message' => 'Données manquantes.'], 400);
            return;
        }

        $result = $this->congeModel->updateCongeDates((int)$id, $start, $end);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Congé mis à jour avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la mise à jour du congé.'], 500);
        }
    }

    /**
     * API: supprime une demande de congé
     */
    public function deleteConge()
    {
        $data = Flight::request()->data;
        $id = $data['id_demande_conge'] ?? null;

        if (!$id) {
            Flight::json(['success' => false, 'message' => 'ID manquant.'], 400);
            return;
        }

        $result = $this->congeModel->deleteConge((int)$id);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Congé supprimé avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la suppression du congé.'], 500);
        }
    }


    /**
     * Génère une couleur HSL unique et consistante à partir d'une chaîne (ex: ID de l'employé).
     * @param string $str
     * @return string
     */
    private function stringToColor($str) {
        $hash = crc32($str);
        $hue = $hash % 360;
        return "hsl({$hue}, 70%, 50%)";
    }
}