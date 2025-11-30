<?php
namespace app\controllers\ressourceHumaine\employe;
use app\models\ressourceHumaine\employe\EmployeStatistiqueModel;
use Flight;

class EmployeStatistiqueController {
    public function statistiques() {
        $month = Flight::request()->query['month'] ?? null;
        $year = Flight::request()->query['year'] ?? null;
        if ($month === '') $month = null;

        $model = new EmployeStatistiqueModel();
        $statsGenre = $model->getEmployesByGenre($month, $year);
        $statsService = $model->getEmployesByService($month, $year);
        $statsDepartement = $model->getEmployesByDepartement($month, $year);
        $statsPoste = $model->getEmployesByPoste($month, $year);
        $statsActivite = $model->getEmployesByActivite($month, $year);

        Flight::render('ressourceHumaine/back/employe/employeStatistique', [
            'statsGenre' => $statsGenre,
            'statsService' => $statsService,
            'statsDepartement' => $statsDepartement,
            'statsPoste' => $statsPoste,
            'statsActivite' => $statsActivite,
            'selectedMonth' => $month,
            'selectedYear' => $year
        ]);
    }
}