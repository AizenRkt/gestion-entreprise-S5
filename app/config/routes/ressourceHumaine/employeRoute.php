<?php

use app\controllers\ressourceHumaine\document\DocumentController;
use app\controllers\ressourceHumaine\employe\EmployeController;

use flight\net\Router;

/** 
 * @var Router $router
*/

// Routes pour la gestion des employés
$EmployeController = new EmployeController();
$router->get('/employes', [ $EmployeController, 'listEmployes' ]); 
$router->post('/employe/update', [ $EmployeController, 'updateEmploye' ]);

$router->get('/ficheEmploye', [ $EmployeController, 'ficheEmploye' ]);

$DocumentController = new DocumentController();
$router->get('/employe/@id/documents', [$DocumentController, 'getDocumentsEmploye']);
$router->post('/employe/@id/document/create', [$DocumentController, 'createDocumentAvecStatut']);

$router->get('/employe/@id', [$EmployeController, 'statutEmpNow']);
$router->get('/employe/@id/historique', [$EmployeController, 'statutEmpHistorique']);
$router->post('/employe/recruter', [$EmployeController, 'recruterEmploye']);
$router->post('/employe/@id/changerPoste', [$EmployeController, 'changerPosteEmploye']);
