<?php

namespace app\controllers\AVIS\referentiel\article;

use Flight;

class ArticleController {

    public function __construct() {
    }

    public function articleFamilleList() {
        Flight::render('AVIS/referentiel/article/articleFamilleList');
    }

    public function articleFamilleSaisie() {
        Flight::render('AVIS/referentiel/article/articleFamilleSaisie');
    }

    public function articleList() {
        Flight::render('AVIS/referentiel/article/articleList');
    }

    public function articleSaisie() {
        Flight::render('AVIS/referentiel/article/articleSaisie');
    }

    public function articleDetail() {
        Flight::render('AVIS/referentiel/article/articleDetail');
    }

    public function articlePrixHistorique() {
        Flight::render('AVIS/referentiel/article/articlePrixHistorique');
    }
}
