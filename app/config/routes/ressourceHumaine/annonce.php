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

Flight::route('GET /annonceListe', function() {
    $controller = new AnnonceController();
    $controller->getAllAnnonces(); 
});

Flight::route('GET /annonceretrait', function() {
    $controller = new AnnonceController();
    $controller->getAllAnnonces2(); 
});

Flight::route('GET /annoncerenouvellement', function() {
    $controller = new AnnonceController();
    $controller->getAllAnnonces3(); 
});

Flight::route('GET /annonceretrait2', function() {
    $controller = new AnnonceController();
    $controller->getDetailAnnonces3(); 
});

Flight::route('GET /annoncerenouvellement2', function() {
    $controller = new AnnonceController();
    $controller->getDetailAnnonces4(); 
});

Flight::route('GET /annoncedetail', function() {
    $controller = new AnnonceController();
    $controller->getDetailAnnonces2(); 
});

/*Flight::route('POST /annonces', function() {
    $controller = new AnnonceController();
    $controller->getFilteredAnnonces(); 
});*/





