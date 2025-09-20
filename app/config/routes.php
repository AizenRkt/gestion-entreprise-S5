<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\AuthController;

//importation lié flight
use flight\Engine;
use flight\net\Router;

//use Flight;

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
$router->get('/organiserEntretien', [ $Controller, 'orgaEntretien' ]);


/* Authentication Routes */
$AuthController = new AuthController();

$router->get('/auth/login', [ $AuthController, 'log' ]); // Affiche le formulaire de connexion
$router->post('/auth/login', [ $AuthController, 'authVerif' ]); // Vérifie la connexion

// Routes pour l'inscription
$router->get('/auth/sign', [ $AuthController, 'sign' ]); // Affiche le formulaire d'inscription
$router->post('/auth/sign', [ $AuthController, 'authInscription' ]); // Traite l'inscription

?>