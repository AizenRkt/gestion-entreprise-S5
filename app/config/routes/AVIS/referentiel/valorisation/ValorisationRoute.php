<?php

use app\controllers\AVIS\referentiel\valorisation\ValorisationController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new ValorisationController();

/* ==============================
 * METHODE VALORISATION - Pages
 * ============================== */
$router->get('/referentiel/valorisation/list', [$controller, 'methodValorisationList']);
$router->get('/referentiel/valorisation/saisie', [$controller, 'methodValorisationSaisie']);
