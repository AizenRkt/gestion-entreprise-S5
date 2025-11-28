<?php

use app\controllers\ressourceHumaine\conge\CongeController;
use flight\net\Router;

/**
 * @var Router $router
 */

$congeController = new CongeController();

$router->get('/backOffice/conge', [$congeController, 'showCongePage']);

$router->get('/conge/demande', [$congeController, 'showDemandeForm']);

// Route pour soumettre la demande
$router->post('/conge/demande', [$congeController, 'submitDemande']);

// Route pour calculer les jours ouvrés (optionnel)
$router->post('/conge/calcul-jours', [$congeController, 'calculerJoursOuvrees']);