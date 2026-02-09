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

    public function mouvementStockDetail($id)
    {
        Flight::render('AVIS/stock/mouvStockDetail', ['id_mouvement_stock' => (int)$id]);
    }


    public function adminStockSettings()
    {
        Flight::render('AVIS/stock/adminSettings');
    }

    public function stockReservations()
    {
        Flight::render('AVIS/stock/reservations');
    }

    public function inventoryPlanning()
    {
        Flight::render('AVIS/stock/inventairePlan');
    }

    public function inventoryCounting()
    {
        Flight::render('AVIS/stock/inventaireComptage');
    }

    public function inventorySheet()
    {
        Flight::render('AVIS/stock/inventaireFiche');
    }

    public function inventoryValidationList()
    {
        Flight::render('AVIS/stock/inventaireValidationList');
    }

    public function inventoryValidation($id)
    {
        Flight::render('AVIS/stock/inventaireValidation', ['id_inventaire_campagne' => (int)$id]);
    }

    public function inventoryAvailability()
    {
        Flight::render('AVIS/stock/inventaireDisponibilite');
    }


}
