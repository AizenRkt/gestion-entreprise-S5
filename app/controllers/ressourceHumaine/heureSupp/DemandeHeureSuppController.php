<?php

namespace app\controllers\ressourceHumaine\heureSupp;

use app\models\ressourceHumaine\heureSupp\DemandeHeureSuppModel;
use Flight;

class DemandeHeureSuppController
{
    private $demandeHeureSuppModel;

    public function __construct()
    {
        $this->demandeHeureSuppModel = new DemandeHeureSuppModel();
    }

    public function showDemandePage()
    {
        Flight::render('ressourceHumaine/back/heureSupp/demande_heureSupp');
    }

    public function submitDemande()
    {
        $data = Flight::request()->data->getData();
        
        $result = $this->demandeHeureSuppModel->creerDemandeHeureSupp($data);
        
        if ($result['success']) {
            Flight::json(['success' => true, 'message' => 'Demande d\'heures supplémentaires soumise avec succès']);
        } else {
            Flight::json(['success' => false, 'message' => $result['message']], 400);
        }
    }
}