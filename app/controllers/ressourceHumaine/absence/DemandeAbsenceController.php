<?php

namespace app\controllers\ressourceHumaine\absence;

use app\models\ressourceHumaine\absence\DemandeAbsenceModel;
use Flight;

class DemandeAbsenceController
{
    private $demandeAbsenceModel;

    public function __construct()
    {
        $this->demandeAbsenceModel = new DemandeAbsenceModel();
    }

    public function showDemandePage()
    {
        // Récupérer les types d'absence disponibles
        $typesAbsence = $this->demandeAbsenceModel->getTypesAbsence();
        
        Flight::render('ressourceHumaine/back/absence/demande_absence', [
            'typesAbsence' => $typesAbsence
        ]);
    }

    public function submitDemande()
    {
        $data = Flight::request()->data->getData();
        
        $result = $this->demandeAbsenceModel->creerDemandeAbsence($data);
        
        if ($result['success']) {
            Flight::json(['success' => true, 'message' => 'Demande d\'absence soumise avec succès']);
        } else {
            Flight::json(['success' => false, 'message' => $result['message']], 400);
        }
    }
}