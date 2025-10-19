<?php

use app\controllers\ressourceHumaine\entretien\entretienController;
use app\controllers\Controller;
use flight\net\Router;

/** 
 * @var Router $router
*/

$Controller = new Controller();
$router->get('/planning', [ $Controller, 'planning' ]);

/* Entretien Routes */
$entretienController = new entretienController();

$router->get('/organiserEntretien', [ $entretienController, 'orgaEntretien' ]);
$router->post('/entretien/creer', [ $entretienController, 'creerEntretien' ]);
$router->get('/entretien/candidat-info', [ $entretienController, 'getCandidatInfo' ]);
$router->get('/entretien/liste', [ $entretienController, 'listerEntretiens' ]);
$router->post('/entretien/modifier', [ $entretienController, 'modifierEntretien' ]);
$router->post('/entretien/supprimer', [ $entretienController, 'supprimerEntretien' ]);
$router->get('/entretien/api/planning', [ $entretienController, 'getEntretiensPlanning' ]);
// $router->post('/entretien/noter', [ $entretienController, 'noterEntretien' ]);
$router->get('/entretien/details', [ $entretienController, 'getEntretienDetails' ]);

// pour noter l'entretien dans les normes
$router->post('/entretien/scoring', [ $entretienController, 'scoringEntretien' ]);
