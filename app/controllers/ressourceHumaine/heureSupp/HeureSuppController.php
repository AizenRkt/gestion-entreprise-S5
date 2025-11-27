<?php

namespace app\controllers\ressourceHumaine\heureSupp;

use app\models\ressourceHumaine\heureSupp\HeureSuppModel;
use Flight;

class HeureSuppController
{
    private $heureSuppModel;

    public function __construct()
    {
        $this->heureSuppModel = new HeureSuppModel();
    }

    public function showHeureSuppPage()
    {
        $heuresSupp = $this->heureSuppModel->getAllHeureSuppDetails();
        Flight::render('ressourceHumaine/back/heureSupp/heureSupp', ['heuresSupp' => $heuresSupp]);
    }

    public function validerHeureSupp()
    {
        $data = Flight::request()->data;
        $id_demande = $data['id_demande_heure_sup'];
        $date_validation = $data['date_validation'];
        $commentaire = $data['commentaire'] ?? null;

        $result = $this->heureSuppModel->processValidation($id_demande, 'valide', $date_validation, $commentaire);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Heure supplémentaire validée avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la validation.']);
        }
    }

    public function refuserHeureSupp()
    {
        $data = Flight::request()->data;
        $id_demande = $data['id_demande_heure_sup'];
        $date_validation = $data['date_validation'];
        $commentaire = $data['commentaire'];

        $result = $this->heureSuppModel->processValidation($id_demande, 'refuse', $date_validation, $commentaire);

        if ($result) {
            Flight::json(['success' => true, 'message' => 'Heure supplémentaire refusée avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors du refus.']);
        }
    }
}

