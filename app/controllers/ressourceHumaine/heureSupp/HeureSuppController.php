<?php

namespace app\controllers\ressourceHumaine\heureSupp;

use app\models\ressourceHumaine\heureSupp\HeureSuppModel;
use Flight;

class HeureSuppController
{
    private $heureSuppModel;

    public function __construct()
    {
        $this->heureSuppModel = new HeureSuppModel();
    }

    public function showHeureSuppPage()
    {
        $heuresSupp = $this->heureSuppModel->getAllHeureSuppDetails();
        Flight::render('ressourceHumaine/back/heureSupp/heureSupp', ['heuresSupp' => $heuresSupp]);
    }
}

