<?php

use app\controllers\ressourceHumaine\employe\EmployeController;

use flight\net\Router;

/** 
 * @var Router $router
*/

// Routes pour la gestion des employés
$EmployeController = new EmployeController();
$router->get('/employes', [ $EmployeController, 'listEmployes' ]); // Liste tous les employés
$router->post('/employe/update', [ $EmployeController, 'updateEmploye' ]); // Liste tous les employés