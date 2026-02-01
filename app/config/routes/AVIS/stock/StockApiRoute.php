<?php

use app\controllers\AVIS\stock\MouvStockApiController;
use app\controllers\AVIS\stock\StockAdminApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

$router->get('/api/stock/mouvements', [MouvStockApiController::class, 'listMovements']);
$router->get('/api/stock/mouvements/@id', [MouvStockApiController::class, 'getMovement']);
$router->get('/api/stock/mouvements/@id/details', [MouvStockApiController::class, 'getMovementLotDetails']);
$router->get('/api/stock/mouvements/@id/details.csv', [MouvStockApiController::class, 'exportMovementLotDetailsCsv']);
$router->get('/api/stock/mouvements/@id/details.pdf', [MouvStockApiController::class, 'exportMovementLotDetailsPdf']);
$router->post('/api/stock/mouvements/create', [MouvStockApiController::class, 'createMovement']);
$router->post('/api/stock/mouvements/@id/validate', [MouvStockApiController::class, 'validateMovement']);

// helpers for UI
$router->get('/api/stock/types', [MouvStockApiController::class, 'listTypes']);
$router->get('/api/stock/depots', [MouvStockApiController::class, 'listDepots']);
$router->get('/api/stock/articles', [MouvStockApiController::class, 'listArticles']);
$router->get('/api/stock/lots', [MouvStockApiController::class, 'getLots']);
$router->post('/api/stock/lots/create', [MouvStockApiController::class, 'createLot']);
$router->get('/api/stock/courant', [MouvStockApiController::class, 'getStockCourant']);
$router->get('/api/stock/reservations', [MouvStockApiController::class, 'listReservations']);

// Admin: per-article defaults and closure operations
$router->get('/api/stock/admin/articles', [StockAdminApiController::class, 'listArticlesDefaults']);
$router->get('/api/stock/admin/methods', [StockAdminApiController::class, 'listMethods']);
$router->post('/api/stock/admin/article/@id/update-defaults', [StockAdminApiController::class, 'updateArticleDefaults']);
$router->get('/api/stock/closure/status', [StockAdminApiController::class, 'getClosureStatus']);
$router->post('/api/stock/closure/open', [StockAdminApiController::class, 'openClosure']);
$router->post('/api/stock/closure/close', [StockAdminApiController::class, 'closeClosure']);
