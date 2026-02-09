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

// Inventory planning page
$router->get('/stock/inventaire/planification', [$controller, 'inventoryPlanning']);
$router->get('/stock/inventaire/comptage', [$controller, 'inventoryCounting']);
$router->get('/stock/inventaire/fiche', [$controller, 'inventorySheet']);
$router->get('/stock/inventaire/validation', [$controller, 'inventoryValidationList']);
$router->get('/stock/inventaire/validation/@id', [$controller, 'inventoryValidation']);
$router->get('/stock/inventaire/disponibilite', [$controller, 'inventoryAvailability']);

