<?php

namespace app\controllers;

use app\controllers\ressourceHumaine\contratEssai\ContratEssaiController;
use app\models\ressourceHumaine\AuthModel;

use Flight;

class Controller
{

    public function __construct()
    {
    }

    public function log()
    {
        Flight::render('auth/log');
    }

    public function sign()
    {
        Flight::render('auth/sign');
    }

    public static function getMenuByUser() {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(["error" => "Utilisateur non connecté"]);
            return;
        }
    
        $id_user = $_SESSION['user']['id_user'];
        $id_service = AuthModel::getServiceByUserId($id_user);

        if (!$id_service || !isset($_SESSION['user']['id_user'])) {
            http_response_code(404);
            echo json_encode(["error" => "Service non trouvé pour cet utilisateur"]);
            return;
        }

        $menuPath = null;
        switch ($id_service) {
            case 8:
                $menuPath = "ui/menu/menuRH";
                break;
            case 1:
                $menuPath = "ui/menu/menuDept";
                break;

            default:
                $menuPath = "ui/menu/menu";
                break;
        }

        return $menuPath;
    }


    public function acceuil()
    {
        Flight::render('ressourceHumaine/acceuil');
    }

    public function annonce()
    {
        Flight::render('ressourceHumaine/annonce');
    }

    public function singleAnnonce()
    {
        Flight::render('ressourceHumaine/annoncePage');
    }

    public function createAnnonce()
    {
        Flight::render('ressourceHumaine/back/creaAnnonce');
    }

    public function candidature()
    {
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

    public function planning()
    {
        Flight::render('ressourceHumaine/back/planning');
    }

    public function orgaEntretien()
    {
        Flight::render('ressourceHumaine/back/orgaEntretien');
    }

    public function backOffice()
    {
        Flight::render('ui/homeDefLayout');
    }
    public function listing() {
        Flight::render('ressourceHumaine/back/employe/listemploye');
    }

    public function contratEssai()
    {
        // Rediriger vers le nouveau contrôleur
        $contratController = new ContratEssaiController();
        return $contratController->contratEssai();
    }

}
