<?php

use app\controllers\ressourceHumaine\jourFerie\JourFerieController;
use flight\net\Router;

/**
 * @var Router $router
 */

$jourFerieController = new JourFerieController();

$router->get('/backOffice/jourFerie', [$jourFerieController, 'showJourFeriePage']);
$router->post('/backOffice/jourFerie/create', [$jourFerieController, 'createJourFerie']);
$router->get('/backOffice/jourFerie/delete/@id', [$jourFerieController, 'deleteJourFerie']);

