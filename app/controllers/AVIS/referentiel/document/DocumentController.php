<?php

namespace app\controllers\AVIS\referentiel\document;

use Flight;

class DocumentController {

    public function __construct() {
    }

    public function documentTypeList() {
        Flight::render('AVIS/referentiel/document/documentTypeList');
    }

    public function documentTypeSaisie() {
        Flight::render('AVIS/referentiel/document/documentTypeSaisie');
    }

    public function documentList() {
        Flight::render('AVIS/referentiel/document/documentList');
    }

    public function documentSaisie() {
        Flight::render('AVIS/referentiel/document/documentSaisie');
    }

    public function documentDetail() {
        Flight::render('AVIS/referentiel/document/documentDetail');
    }
}
