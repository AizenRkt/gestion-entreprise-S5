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

    public function mouvementStockValidation($id)
    {
        Flight::render('AVIS/stock/mouvStockValidate', ['id_mouvement_stock' => (int)$id]);
    }

    public function adminStockSettings()
    {
        Flight::render('AVIS/stock/adminSettings');
    }

    public function stockReservations()
    {
        Flight::render('AVIS/stock/reservations');
    }


}
