<?php
require __DIR__ . '/../../../models/ressourceHumaine/back/creaAnnonce.php';
use app\models\ressourceHumaine\back\creaAnnonce;

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

Flight::route('POST /annonce/create', ['app\controllers\ressourceHumaine\back\creaAnnonce','create']);



