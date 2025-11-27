<?php
namespace app\controllers\ressourceHumaine\employe;
use app\models\ressourceHumaine\employe\EmployeStatistiqueModel;
use Flight;

class EmployeStatistiqueController {
    public function statistiques() {
        $model = new EmployeStatistiqueModel();
        $statsGenre = $model->getEmployesByGenre();
        $statsService = $model->getEmployesByService();
        $statsDepartement = $model->getEmployesByDepartement();
        $statsPoste = $model->getEmployesByPoste();
        $statsActivite = $model->getEmployesByActivite();

        Flight::render('ressourceHumaine/back/employe/employeStatistique', [
            'statsGenre' => $statsGenre,
            'statsService' => $statsService,
            'statsDepartement' => $statsDepartement,
            'statsPoste' => $statsPoste,
            'statsActivite' => $statsActivite
        ]);
    }
}