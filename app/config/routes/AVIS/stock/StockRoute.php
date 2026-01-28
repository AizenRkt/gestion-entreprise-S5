<?php

use app\controllers\AVIS\stock\MouvStockController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new MouvStockController();

$router->get('/stock/mouvement/list', [$controller, 'mouvementStockList']);
$router->get('/stock/mouvement/saisie', [$controller, 'mouvementStockSaisie']);
