<?php
use app\controllers\ressourceHumaine\annonce\annonce as AnnonceController;

Flight::route('GET /annonces', function() {
    $controller = new AnnonceController();
    $controller->getFilteredAnnonces(); 
});

Flight::route('GET /annoncePage', function() {
    $controller = new AnnonceController();
    $controller->getDetailAnnonces(); 
});

/*Flight::route('POST /annonces', function() {
    $controller = new AnnonceController();
    $controller->getFilteredAnnonces(); 
});*/





