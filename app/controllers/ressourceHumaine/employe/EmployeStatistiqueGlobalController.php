<?php

namespace app\controllers\ressourceHumaine\employe;

use app\controllers\Controller;
use app\models\ressourceHumaine\employe\EmployeStatistiqueGlobalModel;
use Flight;

class EmployeStatistiqueGlobalController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new EmployeStatistiqueGlobalModel();
    }

    public function statistiques()
    {
        $annee = Flight::request()->query['annee'] ?? date('Y');
        $data = $this->model->getStatistiquesGlobales($annee);

        // Préparer les données pour les graphiques
        $chartData = [
            'turnover' => [
                'labels' => ['Taux de Turnover (%)'],
                'data' => [$data['taux_turnover']]
            ],
            'absenteisme' => [
                'labels' => ['Taux d\'Absentéisme (%)'],
                'data' => [$data['taux_absenteisme']]
            ],
            'anciennete' => [
                'labels' => ['Ancienneté Moyenne (années)'],
                'data' => [$data['anciennete_moyenne']]
            ]
        ];

        Flight::render('ressourceHumaine/back/employe/EmployeStatistiqueGlobal', [
            'data' => $data,
            'chartData' => $chartData
        ]);
    }
}
