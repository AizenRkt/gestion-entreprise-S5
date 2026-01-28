<?php

namespace app\controllers\AVIS\referentiel\client;

use Flight;

class ClientController {

    public function __construct() {
    }

    public function clientTypeList() {
        Flight::render('AVIS/referentiel/client/clientTypeList');
    }

    public function clientTypeSaisie() {
        Flight::render('AVIS/referentiel/client/clientTypeSaisie');
    }

    public function clientList() {
        Flight::render('AVIS/referentiel/client/clientList');
    }

    public function clientSaisie() {
        Flight::render('AVIS/referentiel/client/clientSaisie');
    }

    public function clientDetail() {
        Flight::render('AVIS/referentiel/client/clientDetail');
    }
}
