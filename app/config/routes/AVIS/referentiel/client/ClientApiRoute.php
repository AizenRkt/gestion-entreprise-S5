<?php

use app\controllers\AVIS\referentiel\client\ClientApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

/* ==============================
 * CLIENT TYPE - API
 * ============================== */
$router->get('/api/referentiel/client-types/all', [ClientApiController::class, 'getAllClientTypes']);
$router->get('/api/referentiel/client-types/@id', [ClientApiController::class, 'getClientTypeById']);
$router->post('/api/referentiel/client-types/create', [ClientApiController::class, 'createClientType']);
$router->put('/api/referentiel/client-types/@id', [ClientApiController::class, 'updateClientType']);
$router->delete('/api/referentiel/client-types/@id', [ClientApiController::class, 'deleteClientType']);

/* ==============================
 * CLIENT - API
 * ============================== */
$router->get('/api/referentiel/clients/all', [ClientApiController::class, 'getAllClients']);
$router->get('/api/referentiel/clients/@id', [ClientApiController::class, 'getClientById']);
$router->post('/api/referentiel/clients/create', [ClientApiController::class, 'createClient']);
$router->put('/api/referentiel/clients/@id', [ClientApiController::class, 'updateClient']);
$router->delete('/api/referentiel/clients/@id', [ClientApiController::class, 'deleteClient']);
$router->get('/api/referentiel/clients/search', [ClientApiController::class, 'searchClient']);
