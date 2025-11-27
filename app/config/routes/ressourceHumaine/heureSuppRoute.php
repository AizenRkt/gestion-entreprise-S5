<?php

use app\controllers\ressourceHumaine\heureSupp\HeureSuppController;
use flight\net\Router;

/**
 * @var Router $router
 */

$heureSuppController = new HeureSuppController();

$router->get('/backOffice/heureSupp', [$heureSuppController, 'showHeureSuppPage']);

