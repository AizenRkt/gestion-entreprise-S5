<?php

namespace app\controllers\ressourceHumaine\conge;

use app\models\ressourceHumaine\conge\CongeModel;
use Flight;

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
}
