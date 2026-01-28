<?php

namespace app\controllers\AVIS\stock;

use Flight;

class MouvStockController
{

    public function __construct()
    {
    }

    public function mouvementStockList()
    {
        Flight::render('AVIS/stock/mouvStockList');
    }

    public function mouvementStockSaisie()
    {
        Flight::render('AVIS/stock/mouvStockSaisie');
    }



}
