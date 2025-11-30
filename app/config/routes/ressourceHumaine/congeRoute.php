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
// API pour récupérer le solde avant validation
$router->get('/api/conge/solde', [$congeController, 'getSoldeForDemande']);
// API pour le planning FullCalendar
$router->get('/api/conges/planning', [$congeController, 'getCongesForPlanning']);

