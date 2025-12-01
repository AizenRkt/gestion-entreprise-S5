<?php

use app\controllers\ressourceHumaine\paie\PaieController;

use flight\net\Router;

/** 
 * @var Router $router
*/

$paieController = new paieController();

$router->get('/paie/fichePaie', [ $paieController, 'fichePaie' ]);
$router->get('/paie/etatPaie', [ $paieController, 'etatPaie' ]);
