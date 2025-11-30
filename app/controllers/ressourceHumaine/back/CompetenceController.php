<?php
namespace app\controllers\ressourceHumaine\back;

use Flight;
use app\models\ressourceHumaine\competence\CompetenceModel;

class CompetenceController {
    private $model;

    public function __construct($db) {
        $db = Flight::db();
        $this->model = new CompetenceModel($db);
    }

    public function getSkillsOverview() {
        $data = $this->model->getSkillsOverview();
        Flight::json($data); 
    }

    public function getEmployeesSkill() {
        $data = $this->model->getEmployeesSkill();
        Flight::json($data); 
    }
}
