<?php
namespace app\controllers\ressourceHumaine\back;

use Flight;
use app\models\ressourceHumaine\paie\PaieModel;
use app\models\ressourceHumaine\heureSupp\HeureSuppModel;

class AssuranceController {
    private $model;

    public function __construct($db) {
        $db = Flight::db();
        $this->model = new PaieModel($db);
    }

    public function tauxAssurance() {
        $data = $this->model->tauxAssurance();
        Flight::json($data); 
    }
    
    public function tauxHeureSup() {
        $data = $this->model->tauxHeureSup();
        Flight::json($data); 
    } 

    public function getPrime($id_employe, $mois, $annee)
    {
        $id_employe = (int)$id_employe;
        $mois = (int)$mois;
        $annee = (int)$annee;

        if (!$id_employe || !$mois || !$annee) {
            Flight::json([
                'success' => false,
                'message' => 'Paramètres invalides.'
            ]);
            return;
        }

        $data = $this->model->getPrime($id_employe, $mois, $annee);

        Flight::json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getAllHeureSuppByEmployeAndDate($id_employe, $mois, $annee)
    {
        $id_employe = (int)$id_employe;
        $mois = (int)$mois;
        $annee = (int)$annee;

        if (!$id_employe || !$mois || !$annee) {
            Flight::json([
                'success' => false,
                'message' => 'Paramètres invalides.'
            ]);
            return;
        }

        $model = new HeureSuppModel();  // fixed
        $data = $model->getAllHeureSuppByEmployeAndDate($id_employe, $mois, $annee);

        Flight::json([
            'success' => true,
            'data' => $data
        ]);
    }
}
