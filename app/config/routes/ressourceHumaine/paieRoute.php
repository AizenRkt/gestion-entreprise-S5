<?php

use app\controllers\ressourceHumaine\paie\PaieController;
use app\controllers\ressourceHumaine\back\AssuranceController;

use flight\net\Router;

/** 
 * @var Router $router
*/

$paieController = new PaieController();

$router->get('/paie/fichePaie', [ $paieController, 'fichePaie' ]);
$router->get('/paie/etatPaie', [ $paieController, 'etatPaie' ]);

Flight::route('GET /api/tauxAssurance', function(){
    $db = Flight::db();
    $controller = new AssuranceController($db); 
    $controller->tauxAssurance();
});
