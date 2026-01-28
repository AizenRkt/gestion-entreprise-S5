<?php

namespace app\controllers\AVIS\referentiel\site;

use Flight;

class SiteController {

    public function __construct() {
    }

    public function depotList() {
        Flight::render('AVIS/referentiel/site/depotList');
    }

    public function depotSaisie() {
        Flight::render('AVIS/referentiel/site/depotSaisie');
    }

    public function siteList() {
        Flight::render('AVIS/referentiel/site/siteList');
    }

    public function siteSaisie() {
        Flight::render('AVIS/referentiel/site/siteSaisie');
    }

    public function siteDetail() {
        Flight::render('AVIS/referentiel/site/siteDetail');
    }
}
