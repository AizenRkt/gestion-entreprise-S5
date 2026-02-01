<?php

use app\controllers\AVIS\stock\MouvStockController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new MouvStockController();

$router->get('/stock/mouvement/list', [$controller, 'mouvementStockList']);
$router->get('/stock/mouvement/saisie', [$controller, 'mouvementStockSaisie']);
$router->get('/stock/mouvement/@id/valider', [$controller, 'mouvementStockValidation']);
// Mouvement detail page
$router->get('/stock/mouvement/@id', [$controller, 'mouvementStockDetail']);

// Detail pages

// Admin settings page
$router->get('/stock/admin/settings', [$controller, 'adminStockSettings']);

// Reservations page
$router->get('/stock/reservations', [$controller, 'stockReservations']);
