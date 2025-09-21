<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\qcm\QcmController;
use app\controllers\ressourceHumaine\candidat\CandidatController;
use app\controllers\ressourceHumaine\AuthController;
use app\controllers\ressourceHumaine\entretien\entretienController;
use app\controllers\ressourceHumaine\contratEssai\ContratEssaiController;

//importation liée flight
use flight\Engine;
use flight\net\Router;

require __DIR__ . '/routes/qcmRoute.php';
require __DIR__ . '/routes/authRoute.php';
require __DIR__ . '/routes/employeRoute.php';

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

/* Entretien Routes */
$entretienController = new entretienController();

$router->get('/organiserEntretien', [ $entretienController, 'orgaEntretien' ]);
$router->post('/entretien/creer', [ $entretienController, 'creerEntretien' ]);
$router->get('/entretien/candidat-info', [ $entretienController, 'getCandidatInfo' ]);
$router->get('/entretien/liste', [ $entretienController, 'listerEntretiens' ]);
$router->post('/entretien/modifier', [ $entretienController, 'modifierEntretien' ]);
$router->post('/entretien/supprimer', [ $entretienController, 'supprimerEntretien' ]);
$router->get('/entretien/api/planning', [ $entretienController, 'getEntretiensPlanning' ]);
$router->post('/entretien/noter', [ $entretienController, 'noterEntretien' ]);
$router->get('/entretien/details', [ $entretienController, 'getEntretienDetails' ]);

/* Contrat d'Essai Routes - NOUVELLES ROUTES */
$contratEssaiController = new ContratEssaiController();

// Page principale des contrats d'essai
$router->get('/contratCrea', [ $contratEssaiController, 'contratEssai' ]);

// Accepter un contrat (AJAX)
$router->post('/contrat/accepter', [ $contratEssaiController, 'accepterContrat' ]);

// Générer le PDF du contrat
$router->get('/contrat/generate/@id', [ $contratEssaiController, 'generatePdf' ]);

// API pour récupérer les candidats recommandés
$router->get('/api/candidats/recommandes', [ $contratEssaiController, 'getCandidatsRecommandesAPI' ]);

// Créer un contrat d'essai officiel
$router->post('/contrat/creer-officiel', [ $contratEssaiController, 'creerContratOfficiel' ]);

?>