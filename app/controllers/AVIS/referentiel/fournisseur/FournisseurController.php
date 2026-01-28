<?php

namespace app\controllers\AVIS\referentiel\fournisseur;

use Flight;

class FournisseurController {

    public function __construct() {
    }

    public function fournisseurList() {
        Flight::render('AVIS/referentiel/fournisseur/fournisseurList');
    }

    public function fournisseurSaisie() {
        Flight::render('AVIS/referentiel/fournisseur/fournisseurSaisie');
    }

    public function fournisseurDetail() {
        Flight::render('AVIS/referentiel/fournisseur/fournisseurDetail');
    }

    public function fournisseurArticleList() {
        Flight::render('AVIS/referentiel/fournisseur/fournisseurArticleList');
    }
}
