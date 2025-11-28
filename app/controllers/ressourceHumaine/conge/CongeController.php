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

    public function showDemandeForm()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user']['id_employe'])) {
            Flight::redirect('/log');
            return;
        }

        $model = new CongeModel();
        $typesConge = $model->getAllTypesConge();
        $soldeActuel = $model->getSoldeConge($_SESSION['user']['id_employe']);

        Flight::render('ressourceHumaine/back/conge/demande', [
            'typesConge' => $typesConge,
            'soldeActuel' => $soldeActuel
        ]);
    }
    private function validateDemande($data)
    {
        $errors = [];

        // Vérification type de congé
        if (empty($data['id_type_conge'])) {
            $errors['id_type_conge'] = "Le type de congé est obligatoire.";
        }

        // Vérification date début
        if (empty($data['date_debut'])) {
            $errors['date_debut'] = "La date de début est obligatoire.";
        }

        // Vérification date fin
        if (empty($data['date_fin'])) {
            $errors['date_fin'] = "La date de fin est obligatoire.";
        }

        // Vérification cohérence des dates
        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
                $errors['dates'] = "La date de fin doit être après la date de début.";
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

        $data = Flight::request()->data;

        // Validation des données
        $errors = $this->validateDemande($data);
        if (!empty($errors)) {
            Flight::json(['success' => false, 'message' => 'Données invalides', 'errors' => $errors]);
            return;
        }

        $model = new CongeModel();
        $result = $model->createDemandeConge(
            $_SESSION['user']['id_employe'],
            $data['id_type_conge'],
            $data['date_debut'],
            $data['date_fin'],
            $data['motif'] ?? ''
        );

        if ($result['success']) {
            Flight::json(['success' => true, 'message' => 'Demande de congé soumise avec succès']);
        } else {
            Flight::json(['success' => false, 'message' => $result['message']]);
        }
    }
}
