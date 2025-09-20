<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\qcm\QcmController;
use app\controllers\ressourceHumaine\contratEssai\ContratEssaiController;
use app\controllers\ressourceHumaine\entretien\EntretienController;

//importation liée flight
use flight\Engine;
use flight\net\Router;

//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
*/

$Controller = new Controller();
$router->get('/', [ $Controller, 'acceuil' ]);
$router->get('/log', [ $Controller, 'log' ]);
$router->get('/sign', [ $Controller, 'sign' ]);
$router->get('/annonce', [ $Controller, 'annonce' ]);
$router->get('/annoncePage', [ $Controller, 'singleAnnonce' ]);
$router->get('/annonceCrea', [ $Controller, 'createAnnonce' ]);
$router->get('/candidature', [ $Controller, 'candidature' ]);
$router->get('/planning', [ $Controller, 'planning' ]);
$router->get('/organiserEntretien', [ $Controller, 'orgaEntretien' ]);

$contratController = new ContratEssaiController();

$router->get('/contrats', [$contratController, 'getAll']);           
$router->get('/contrats/@id', [$contratController, 'getById']);      
$router->post('/contrats/create', [$contratController, 'create']);   
$router->post('/contrats/update/@id', [$contratController, 'update']); 
$router->get('/contrats/delete/@id', [$contratController, 'delete']);  

// Route spéciale : génération PDF
$router->get('/contrats/pdf/@id', [$contratController, 'generatePdf']); 


$entretienController = new EntretienController();
$router->get('/entretiens', [$entretienController, 'getAll']);
$router->post('/entretiens/create', [$entretienController, 'create']);
$router->get('/entretiens/delete/@id', [$entretienController, 'delete']);

// Route pour l'API des entretiens par mois
$router->get('/api/entretiens/month', [$entretienController, 'getEntretiensByMonth']);

$router->post('/organiserEntretien', [$entretienController, 'create']);
$router->get('/api/entretiens/day', [$entretienController, 'getEntretiensByDay']);



$qcmController = new QcmController();

// Routes pour les QCM
$router->get('/qcm', [$qcmController, 'getAll']);

?>