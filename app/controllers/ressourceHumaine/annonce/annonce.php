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
        $annonces = $this->model->getAllAnnonces();

        $diplomes = $this->model->getAllDiplomes(); 

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/back/listingAnnonce', [
            'annonces' => $annonces,
            'diplomes' => $diplomes,
            'villes' => $villes
        ]); 
    }

    public function getAllAnnonces2() {
        $id = $_GET['id'] ?? null;

        $this->model->retraitaAnnonce($id);

        $annonces = $this->model->getAllAnnonces();

        $diplomes = $this->model->getAllDiplomes(); 

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/back/listingAnnonce', [
            'annonces' => $annonces,
            'diplomes' => $diplomes,
            'villes' => $villes
        ]); 
    }

    public function getAllAnnonces3() {
        $id = $_GET['id'] ?? null;

        $this->model->renouvellementAnnonce($id);

        $annonces = $this->model->getAllAnnonces();

        $diplomes = $this->model->getAllDiplomes(); 

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/back/listingAnnonce', [
            'annonces' => $annonces,
            'diplomes' => $diplomes,
            'villes' => $villes
        ]); 
    }

    public function getFilteredAnnonces() {
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

    public function getDetailAnnonces() {
        $id = $_GET['id'] ?? null;

        $annonces = $this->model->getDetailAnnonces($id); 

        Flight::render('ressourceHumaine/annoncePage', [
            'annonces' => $annonces,
        ]); 
    }
}

