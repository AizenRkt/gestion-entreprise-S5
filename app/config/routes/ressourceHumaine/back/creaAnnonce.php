<?php
// require __DIR__ . '/../../../models/ressourceHumaine/back/creaAnnonce.php';
use app\controllers\Controller;
use app\models\ressourceHumaine\back\creaAnnonce;

$Controller = new Controller();

$router->get('/annonceCrea', [ $Controller, 'createAnnonce' ]);

Flight::route('GET /api/diplomes', function() {
    $db = Flight::db();                
    $controller = new creaAnnonce($db); 
    Flight::json($controller->getAllDiplome());
});



Flight::route('GET /api/competences', function() {
    $db = Flight::db();
    $controller = new creaAnnonce($db);
    Flight::json($controller->getAllCompetence());
});

Flight::route('GET /api/ville', function() {
    $db = Flight::db();
    $controller = new creaAnnonce($db);
    Flight::json($controller->getAllVille());
});

Flight::route('GET /api/profil', function() {
    $db = Flight::db();
    $controller = new creaAnnonce($db);
    Flight::json($controller->getAllProfil());
});

Flight::route('POST /annonce/create', ['app\controllers\ressourceHumaine\back\creaAnnonce','create']);



