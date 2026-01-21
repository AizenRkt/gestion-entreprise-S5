<?php

namespace app\controllers\AVIS\KPI;

use Flight;

class KpiController
{
    /* ==============================
     * KPI - DIRECTION GÉNÉRALE
     * ============================== */
    public static function direction()
    {
        Flight::render('AVIS/kpi/direction', [
            'title' => 'Tableau de bord - Direction Générale'
        ]);
    }

    /* ==============================
     * KPI - ACHATS / SUPPLY CHAIN
     * ============================== */
    public static function achats()
    {
        Flight::render('AVIS/kpi/achats', [
            'title' => 'KPI Achats & Supply Chain'
        ]);
    }

    /* ==============================
     * KPI - MAGASIN / STOCK
     * ============================== */
    public static function stock()
    {
        Flight::render('AVIS/kpi/stock', [
            'title' => 'KPI Stock & Magasin'
        ]);
    }

    /* ==============================
     * KPI - VENTES / COMMERCIAL
     * ============================== */
    public static function ventes()
    {
        Flight::render('AVIS/kpi/ventes', [
            'title' => 'KPI Ventes & Commercial'
        ]);
    }

    /* ==============================
     * KPI - FINANCE / DAF
     * ============================== */
    public static function finance()
    {
        Flight::render('AVIS/kpi/finance', [
            'title' => 'KPI Finance & DAF'
        ]);
    }
}
