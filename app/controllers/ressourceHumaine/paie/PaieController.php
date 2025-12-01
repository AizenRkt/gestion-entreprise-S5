<?php

namespace app\controllers\ressourceHumaine\paie;

use app\models\ressourceHumaine\paie\PaieModel;

use Flight;


class PaieController {
    public function fichePaie() {
        Flight::render('ressourceHumaine/back/paie/fichePaie');    
    }

    public function etatPaie() {
        Flight::render('ressourceHumaine/back/paie/etatPaie');
    }
}
