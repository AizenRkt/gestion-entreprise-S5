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
$router->get('/employes', [ $EmployeController, 'listEmployes' ]); // Liste tous les employés
$router->post('/employes', [ $EmployeController, 'listEmployes' ]); // Liste tous les employés avec filtres
$router->post('/employe/update', [ $EmployeController, 'updateEmploye' ]); // Liste tous les employés

$EmployeStatistiqueController = new EmployeStatistiqueController();
$router->get('/employes/statistiques', [ $EmployeStatistiqueController, 'statistiques' ]); // Statistiques des employés

$EmployeStatistiqueGlobalController = new EmployeStatistiqueGlobalController();
$router->get('/employes/statistiques-globales', [ $EmployeStatistiqueGlobalController, 'statistiques' ]); // Statistiques globales des employés