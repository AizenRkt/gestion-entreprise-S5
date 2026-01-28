<?php

use app\controllers\AVIS\referentiel\valorisation\ValorisationApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

/* ==============================
 * METHODE VALORISATION - API
 * ============================== */
$router->get('/api/referentiel/valorisations/all', [ValorisationApiController::class, 'getAllMethodes']);
$router->get('/api/referentiel/valorisations/@id', [ValorisationApiController::class, 'getMethodeById']);
$router->get('/api/referentiel/valorisations/code/@code', [ValorisationApiController::class, 'getMethodeByCode']);
$router->post('/api/referentiel/valorisations/create', [ValorisationApiController::class, 'createMethode']);
$router->put('/api/referentiel/valorisations/@id', [ValorisationApiController::class, 'updateMethode']);
$router->delete('/api/referentiel/valorisations/@id', [ValorisationApiController::class, 'deleteMethode']);
