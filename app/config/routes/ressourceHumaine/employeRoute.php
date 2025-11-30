<?php

use app\controllers\ressourceHumaine\employe\EmployeController;
use app\controllers\ressourceHumaine\employe\EmployeStatistiqueController;
use app\controllers\ressourceHumaine\employe\EmployeStatistiqueGlobalController;

use flight\net\Router;

/** 
 * @var Router $router
*/

// Routes pour la gestion des employés
$EmployeController = new EmployeController();
$router->get('/employes', [ $EmployeController, 'listEmployes' ]);

// Route pour les alertes
$router->get('/employes/alertes', [ $EmployeController, 'alertesEmployes' ]);

// Routes de update (présentes dans les deux branches)
$router->post('/employes', [ $EmployeController, 'listEmployes' ]);
$router->post('/employe/update', [ $EmployeController, 'updateEmploye' ]);

// Routes ajoutées dans ta branche ayt-1
$router->get('/backOffice/user/parametre', [$EmployeController, 'getUserProfile']);
$router->post('/backOffice/user/update', [$EmployeController, 'updateUserProfile']);

// Routes statistique (venant de main)
$EmployeStatistiqueController = new EmployeStatistiqueController();
$router->get('/employes/statistiques', [ $EmployeStatistiqueController, 'statistiques' ]);

$EmployeStatistiqueGlobalController = new EmployeStatistiqueGlobalController();
$router->get('/employes/statistiques-globales', [ $EmployeStatistiqueGlobalController, 'statistiques' ]);
