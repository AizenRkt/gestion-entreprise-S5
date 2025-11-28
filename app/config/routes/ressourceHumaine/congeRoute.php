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

$router->get('/conge/demande', [$congeController, 'showDemandeForm']);

// Route pour soumettre la demande
$router->post('/conge/demande', [$congeController, 'submitDemande']);

// Route pour calculer les jours ouvrés (optionnel)
$router->post('/conge/calcul-jours', [$congeController, 'calculerJoursOuvrees']);