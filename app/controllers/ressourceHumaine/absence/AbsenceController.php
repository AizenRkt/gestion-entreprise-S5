<?php

namespace app\controllers\ressourceHumaine\absence;

use app\models\ressourceHumaine\absence\AbsenceModel;
use app\models\ressourceHumaine\AuthModel;
use Flight;

class AbsenceController
{
    private $absenceModel;


    public function __construct()
    {
        $this->absenceModel = new AbsenceModel();
    }


    public function showAbsencePage()
    {
        $absences = $this->absenceModel->getAllAbsenceDetails();
        Flight::render('ressourceHumaine/back/absence/absence', ['absences' => $absences]);
    }

}
