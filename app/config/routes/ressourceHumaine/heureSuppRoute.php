<?php

use app\controllers\ressourceHumaine\heureSupp\HeureSuppController;
use app\controllers\ressourceHumaine\heureSupp\DemandeHeureSuppController;
use flight\net\Router;

/**
 * @var Router $router
 */

$heureSuppController = new HeureSuppController();
$demandeHeureSuppController = new DemandeHeureSuppController();

$router->get('/backOffice/heureSupp', [$heureSuppController, 'showHeureSuppPage']);
$router->post('/backOffice/heureSupp/valider', [$heureSuppController, 'validerHeureSupp']);
$router->post('/backOffice/heureSupp/refuser', [$heureSuppController, 'refuserHeureSupp']);



// Routes pour la demande d'heures supplémentaires
$router->get('/heureSupp/demande', [$demandeHeureSuppController, 'showDemandePage']);
$router->post('/heureSupp/demande/submit', [$demandeHeureSuppController, 'submitDemande']);