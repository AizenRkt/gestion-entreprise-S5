<?php

namespace app\controllers\AVIS\referentiel\valorisation;

use Flight;

class ValorisationController {

    public function __construct() {
    }

    public function methodValorisationList() {
        Flight::render('AVIS/referentiel/valorisation/methodValorisationList');
    }

    public function methodValorisationSaisie() {
        Flight::render('AVIS/referentiel/valorisation/methodValorisationSaisie');
    }
}
