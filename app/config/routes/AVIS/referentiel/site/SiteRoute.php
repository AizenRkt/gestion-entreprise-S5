<?php

use app\controllers\AVIS\referentiel\site\SiteController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new SiteController();

/* ==============================
 * DEPOT - Pages
 * ============================== */
$router->get('/referentiel/depot/list', [$controller, 'depotList']);
$router->get('/referentiel/depot/saisie', [$controller, 'depotSaisie']);

/* ==============================
 * SITE - Pages
 * ============================== */
$router->get('/referentiel/site/list', [$controller, 'siteList']);
$router->get('/referentiel/site/saisie', [$controller, 'siteSaisie']);
$router->get('/referentiel/site/detail', [$controller, 'siteDetail']);
