<?php

use app\controllers\AVIS\KPI\KpiController;
use flight\net\Router;

/**
 * @var Router $router
 */

$kpiController = new KpiController();

/* ==============================
 * KPI - Direction Générale
 * ============================== */
$router->get('/kpi/direction', [$kpiController, 'direction']);

/* ==============================
 * KPI - Achats / Supply Chain
 * ============================== */
$router->get('/kpi/achats', [$kpiController, 'achats']);

/* ==============================
 * KPI - Stock / Magasin
 * ============================== */
$router->get('/kpi/stock', [$kpiController, 'stock']);

/* ==============================
 * KPI - Ventes / Commercial
 * ============================== */
$router->get('/kpi/ventes', [$kpiController, 'ventes']);

/* ==============================
 * KPI - Finance / DAF
 * ============================== */
$router->get('/kpi/finance', [$kpiController, 'finance']);
