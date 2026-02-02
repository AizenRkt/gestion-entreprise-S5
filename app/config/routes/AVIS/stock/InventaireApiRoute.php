<?php

use app\controllers\AVIS\stock\InventaireApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

 // =========================
 // INVENTAIRE – CORE
 // =========================

$router->get('/api/stock/inventaires', [
    InventaireApiController::class,
    'listInventaires'
]);

$router->get('/api/stock/inventaires/@id', [
    InventaireApiController::class,
    'getInventaire'
]);

$router->post('/api/stock/inventaires/create', [
    InventaireApiController::class,
    'createInventaire'
]);

$router->post('/api/stock/inventaires/@id/lignes', [
    InventaireApiController::class,
    'saveInventaireLignes'
]);

$router->post('/api/stock/inventaires/@id/validate', [
    InventaireApiController::class,
    'validateInventaire'
]);

$router->post('/api/stock/inventaires/@id/cancel', [
    InventaireApiController::class,
    'cancelInventaire'
]);


// =========================
 // INVENTAIRE – HELPERS UI
 // =========================

$router->get('/api/stock/inventaires/@id/lignes', [
    InventaireApiController::class,
    'getInventaire'
]);

$router->get('/api/stock/inventaires/depot/@id/articles', [
    InventaireApiController::class,
    'listArticlesByDepot'
]);

