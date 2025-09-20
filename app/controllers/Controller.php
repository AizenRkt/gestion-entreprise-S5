<?php

namespace app\controllers;

use app\models\Status;
use Flight;

class Controller {

    public function __construct() {
    }

    public function log() {
        Flight::render('auth/log');
    }

    public function sign() {
        Flight::render('auth/sign');        
    }

    public function acceuil() {
        $status = new Status(); 
        $data = $status->getStatus();

        Flight::render('ressourceHumaine/acceuil', ['status' => $data]);
    }

    public function annonce() {
        Flight::render('ressourceHumaine/annonce');
    }

    public function singleAnnonce() {
        Flight::render('ressourceHumaine/annoncePage');
    }

    public function createAnnonce() {
        Flight::render('ressourceHumaine/back/creaAnnonce');
    }

    public function candidature() {
        $diplomeModel = new \app\models\ressourceHumaine\diplome\DiplomeModel();
        $competenceModel = new \app\models\ressourceHumaine\competence\CompetenceModel();
        $villeModel = new \app\models\ressourceHumaine\ville\VilleModel();
        $diplomes = $diplomeModel->getAll();
        $competences = $competenceModel->getAll();
        $villes = $villeModel->getAll();
        Flight::render('ressourceHumaine/candidature', [
            'diplomes' => $diplomes,
            'competences' => $competences,
            'villes' => $villes
        ]);
    }

    public function planning() {
        Flight::render('ressourceHumaine/back/planning2');
    }

    public function orgaEntretien() {
        Flight::render('ressourceHumaine/back/orgaEntretien');
    }

    public function backOffice(){
        Flight::render('ui/homeDefLayout');
    }

    public function contratEssai() {
        Flight::render('ressourceHumaine/back/contratEssai');
    }

}
