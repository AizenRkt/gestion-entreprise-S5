<?php
namespace app\controllers\ressourceHumaine\annonce;

use app\models\ressourceHumaine\annonce\annonce as annonceModel;
use Flight;
use PDO;

class annonce {

    private $model;

    public function __construct() {
        $db = Flight::db();
        $this->model = new annonceModel($db);
    }

    public function getAllAnnonces() {
    $keyword = $_GET['keyword'] ?? null;
    $diplome = $_GET['diplome'] ?? null;
    $ville   = $_GET['ville'] ?? null;

    $annonces = $this->model->getFilteredAnnonces($keyword, $diplome, $ville);

    $diplomes = $this->model->getAllDiplomes(); 

    $villes = $this->model->getAllVilles(); 

    Flight::render('ressourceHumaine/annonce', [
        'annonces' => $annonces,
        'diplomes' => $diplomes,
        'villes' => $villes
    ]); 
}


    
}

