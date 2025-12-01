<?php

use app\controllers\ressourceHumaine\paie\PaieController;
use app\controllers\ressourceHumaine\back\AssuranceController;

use flight\net\Router;

/** 
 * @var Router $router
*/

$paieController = new PaieController();

// Route for fiche de paie with parameters
$router->get('/paie/fichePaie/@id_employe/@mois/@annee', function($id_employe, $mois, $annee) use ($paieController) {
    $paieController->fichePaie((int)$id_employe, (int)$mois, (int)$annee);
});

$router->get('/paie/etatPaie', [ $paieController, 'etatPaie' ]);

Flight::route('GET /api/tauxAssurance', function(){
    $db = Flight::db();
    $controller = new AssuranceController($db); 
    $controller->tauxAssurance();
});

Flight::route('GET /api/tauxHeureSup', function(){
    $db = Flight::db();
    $controller = new AssuranceController($db); 
    $controller->tauxHeureSup();
});

Flight::route('GET /api/heures-supp/@id_employe/@mois/@annee', function($id_employe, $mois, $annee){
    $db = Flight::db();
    $controller = new AssuranceController($db);
    $controller->getAllHeureSuppByEmployeAndDate((int)$id_employe, (int)$mois, (int)$annee);
});

Flight::route('GET /api/prime/@id_employe/@mois/@annee', function($id_employe, $mois, $annee){
    $db = Flight::db();
    $controller = new AssuranceController($db);
    $controller->getPrime((int)$id_employe, (int)$mois, (int)$annee);
});

