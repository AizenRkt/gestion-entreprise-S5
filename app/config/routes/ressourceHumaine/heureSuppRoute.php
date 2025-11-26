<?php

use app\controllers\ressourceHumaine\heureSupp\HeureSuppController;
use flight\net\Router;

/**
 * @var Router $router
 */

$heureSuppController = new HeureSuppController();

$router->get('/backOffice/heureSupp', [$heureSuppController, 'showHeureSuppPage']);
$router->post('/backOffice/heureSupp/valider', [$heureSuppController, 'validerHeureSupp']);
$router->post('/backOffice/heureSupp/refuser', [$heureSuppController, 'refuserHeureSupp']);

