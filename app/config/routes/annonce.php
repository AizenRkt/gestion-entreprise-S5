<?php
use app\controllers\ressourceHumaine\annonce\annonce as AnnonceController;

Flight::route('GET /annonces', function() {
    $controller = new AnnonceController();
    $controller->getAllAnnonces(); // this will render the view directly
});





