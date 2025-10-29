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

        $profils = $this->model->getAllProfils();

        $diplomes = $this->model->getAllDiplomes(); 

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/back/listingAnnonce', [
            'annonces' => $annonces,
            'pfs' => $profils,
            'diplomes' => $diplomes,
            'villes' => $villes
        ]); 
    }

    public function getAllAnnonces2() {
        $id = $_GET['id'] ?? null;

        $this->model->retraitaAnnonce($id);

        $annonces = $this->model->getAllAnnonces();

        $profils = $this->model->getAllProfils();

        $diplomes = $this->model->getAllDiplomes(); 

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/back/listingAnnonce', [
            'annonces' => $annonces,
            'pfs' => $profils,
            'diplomes' => $diplomes,
            'villes' => $villes
        ]); 
    }

    public function getAllAnnonces3() {
        $id = $_GET['id'] ?? null;

        $this->model->renouvellementAnnonce($id);

        $annonces = $this->model->getAllAnnonces();

        $profils = $this->model->getAllProfils();

        $diplomes = $this->model->getAllDiplomes(); 

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/back/listingAnnonce', [
            'annonces' => $annonces,
            'pfs' => $profils,
            'diplomes' => $diplomes,
            'villes' => $villes
        ]); 
    }

    public function getFilteredAnnonces() {
        $keyword = $_GET['keyword'] ?? null;
        $diplome = $_GET['diplome'] ?? null;
        $ville   = $_GET['ville'] ?? null;
        $profil   = $_GET['profil'] ?? null;


        $annonces = $this->model->getFilteredAnnonces($keyword, $diplome, $ville, $profil);

        $diplomes = $this->model->getAllDiplomes();
        
        $profils = $this->model->getAllProfils();

        $villes = $this->model->getAllVilles(); 

        Flight::render('ressourceHumaine/annonce', [
            'annonces' => $annonces,
            'pfs' => $profils,
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

    public function getDetailAnnonces2() {
        $id = $_GET['id'] ?? null;

        $annonces = $this->model->getDetailAnnonces($id); 

        Flight::render('ressourceHumaine/back/detailAnnonce', [
            'annonces' => $annonces,
        ]); 
    }
    
    public function getDetailAnnonces3() {
        $id = $_GET['id'] ?? null;
        $this->model->retraitaAnnonce($id);
        $annonces = $this->model->getDetailAnnonces($id); 

        Flight::render('ressourceHumaine/back/detailAnnonce', [
            'annonces' => $annonces,
        ]); 
    }

    public function getDetailAnnonces4() {
        $id = $_GET['id'] ?? null;
        $this->model->renouvellementAnnonce($id);
        $annonces = $this->model->getDetailAnnonces($id); 

        Flight::render('ressourceHumaine/back/detailAnnonce', [
            'annonces' => $annonces,
        ]); 
    }
}

