<?php

use app\controllers\AVIS\referentiel\site\SiteApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

/* ==============================
 * DEPOT - API
 * ============================== */
$router->get('/api/referentiel/depots/all', [SiteApiController::class, 'getAllDepots']);
$router->get('/api/referentiel/depots/@id', [SiteApiController::class, 'getDepotById']);
$router->post('/api/referentiel/depots/create', [SiteApiController::class, 'createDepot']);
$router->put('/api/referentiel/depots/@id', [SiteApiController::class, 'updateDepot']);
$router->delete('/api/referentiel/depots/@id', [SiteApiController::class, 'deleteDepot']);

/* ==============================
 * SITE - API
 * ============================== */
$router->get('/api/referentiel/sites/all', [SiteApiController::class, 'getAllSites']);
$router->get('/api/referentiel/sites/@id', [SiteApiController::class, 'getSiteById']);
$router->post('/api/referentiel/sites/create', [SiteApiController::class, 'createSite']);
$router->put('/api/referentiel/sites/@id', [SiteApiController::class, 'updateSite']);
$router->delete('/api/referentiel/sites/@id', [SiteApiController::class, 'deleteSite']);
$router->get('/api/referentiel/sites/depots/@id_site', [SiteApiController::class, 'getSiteWithDepots']);

/* ==============================
 * SITE DEPOT - API
 * ============================== */
$router->get('/api/referentiel/site-depots/all', [SiteApiController::class, 'getAllSiteDepots']);
$router->get('/api/referentiel/site-depots/@id', [SiteApiController::class, 'getSiteDepotById']);
$router->get('/api/referentiel/site-depots/site/@id_site', [SiteApiController::class, 'getSiteDepotByIdSite']);
$router->post('/api/referentiel/site-depots/create', [SiteApiController::class, 'createSiteDepot']);
$router->delete('/api/referentiel/site-depots/@id', [SiteApiController::class, 'deleteSiteDepot']);
