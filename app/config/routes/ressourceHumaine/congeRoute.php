<?php

use app\controllers\ressourceHumaine\conge\CongeController;
use flight\net\Router;

/**
 * @var Router $router
 */

$congeController = new CongeController();

$router->get('/backOffice/conge', [$congeController, 'showCongePage']);
$router->post('/backOffice/conge/valider', [$congeController, 'validerConge']);
$router->post('/backOffice/conge/refuser', [$congeController, 'refuserConge']);

