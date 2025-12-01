<?php
namespace app\controllers\ressourceHumaine\back;

use Flight;
use app\models\ressourceHumaine\paie\PaieModel;

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
}
