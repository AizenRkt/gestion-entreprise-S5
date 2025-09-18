<?php

use app\controllers\Controller;
use app\Controllers\ressourceHumaine\AuthController;

//importation lié flight
use flight\Engine;
use flight\net\Router;

//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
*/


$Controller = new Controller();
$AuthController = new AuthController();
$router->get('/', [ $Controller, 'acceuil' ]);
$router->get('/log', [ $Controller, 'log' ]);
$router->get('/sign', [ $Controller, 'sign' ]);
$router->get('/annonce', [ $Controller, 'annonce' ]);
$router->get('/annoncePage', [ $Controller, 'singleAnnonce' ]);
$router->get('/annonceCrea', [ $Controller, 'createAnnonce' ]);
$router->get('/candidature', [ $Controller, 'candidature' ]);
$router->get('/planning', [ $Controller, 'planning' ]);
$router->get('/organiserEntretien', [ $Controller, 'orgaEntretien' ]);

/* Authentication Routes */
$router->post('/auth/login', [ $AuthController, 'login' ]);

?>