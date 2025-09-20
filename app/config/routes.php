<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\qcm\QcmController;
use app\controllers\ressourceHumaine\candidat\CandidatController;
use app\controllers\ressourceHumaine\AuthController;
use app\controllers\ressourceHumaine\entretien\entretienController; // Nouveau import

//importation liée flight
use flight\Engine;
use flight\net\Router;

require __DIR__ . '/routes/qcmRoute.php';

/** 
 * @var Router $router 
 * @var Engine $app
*/

$Controller = new Controller();
$router->get('/', [ $Controller, 'acceuil' ]);
$router->get('/log', [ $Controller, 'log' ]);
$router->get('/sign', [ $Controller, 'sign' ]);

$router->get('/annonceCrea', [ $Controller, 'createAnnonce' ]);
$router->get('/candidature', [ $Controller, 'candidature' ]);
$router->get('/planning', [ $Controller, 'planning' ]);
$router->get('/planning2', [ $Controller, 'planning2' ]);
$router->get('/backOffice',[$Controller,'backOffice']);

/* Authentication Routes */
$AuthController = new AuthController();
$qcmController = new QcmController();
$candidatController = new CandidatController();

// Routes d'authentification
$router->get('/auth/login', [ $AuthController, 'log' ]);
$router->post('/auth/login', [ $AuthController, 'authVerif' ]);
$router->get('/auth/sign', [ $AuthController, 'sign' ]);
$router->post('/auth/sign', [ $AuthController, 'authInscription' ]);

/* Entretien Routes - NOUVELLES ROUTES */
$entretienController = new entretienController();

// Affichage du formulaire d'organisation d'entretien
$router->get('/organiserEntretien', [ $entretienController, 'orgaEntretien' ]);

// Création d'un entretien (AJAX)
$router->post('/entretien/creer', [ $entretienController, 'creerEntretien' ]);

// Récupération des informations d'un candidat (AJAX)
$router->get('/entretien/candidat-info', [ $entretienController, 'getCandidatInfo' ]);

// Liste des entretiens
$router->get('/entretien/liste', [ $entretienController, 'listerEntretiens' ]);

// Modification d'un entretien
$router->post('/entretien/modifier', [ $entretienController, 'modifierEntretien' ]);

// Suppression d'un entretien
$router->post('/entretien/supprimer', [ $entretienController, 'supprimerEntretien' ]);

// Récupération des entretiens pour le planning
$router->get('/entretien/api/planning', [ $entretienController, 'getEntretiensPlanning' ]);

// NOUVELLE ROUTE : Noter un entretien
$router->post('/entretien/noter', [ $entretienController, 'noterEntretien' ]);

// NOUVELLE ROUTE : Récupérer les détails d'un entretien
$router->get('/entretien/details', [ $entretienController, 'getEntretienDetails' ]);

?>