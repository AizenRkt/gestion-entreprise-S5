<?php

namespace app\controllers\ressourceHumaine\paie;

use app\models\ressourceHumaine\paie\PaieModel;

use Flight;


class PaieController {
    
    public function fichePaie(int $id_employe, int $mois, int $annee) {
    Flight::render('ressourceHumaine/back/paie/fichePaie', [
        'id_employe' => $id_employe,
        'mois' => $mois,
        'annee' => $annee
    ]);
}


    public function etatPaie() {
        Flight::render('ressourceHumaine/back/paie/etatPaie');
    }

    
       
}
