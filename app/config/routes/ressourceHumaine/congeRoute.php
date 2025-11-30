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
// API pour la mise à jour des dates de congé
$router->post('/api/conge/update', [$congeController, 'updateCongeDate']);
// API pour la suppression de congé
$router->post('/api/conge/delete', [$congeController, 'deleteConge']);

