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
}
