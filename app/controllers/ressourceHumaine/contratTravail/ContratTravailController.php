<?php

namespace app\controllers\ressourceHumaine\contratTravail;

use app\models\ressourceHumaine\employe\EmployeModel;
use app\models\ressourceHumaine\contratTravail\ContratTravailTypeModel;
use app\models\ressourceHumaine\contratTravail\ContratTravailModel;

use Flight;

class ContratTravailController
{

    private $employeModel;
    private $contratTravailModel;
    private $contratTypeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        $this->contratTypeModel = new ContratTravailTypeModel();
        $this->contratTravailModel = new ContratTravailModel();
    }

    public function contratTravail() {
        Flight::render('ressourceHumaine/back/contratTravail/contratTravail');    
    }

    public function creerCDI($id) {

        $debut = Flight::request()->query->debut ?? date('Y-m-d');
        $salaire_base = Flight::request()->query->salaire_base ?? null;
        $date_signature = Flight::request()->query->date_signature ?? null;
        $fin = null; 

        $employe = $this->employeModel->getById($id);
        if (!$employe) {
            Flight::json(['success' => false, 'message' => 'Employé introuvable']);
            return;
        }

        $typeCDI = $this->contratTypeModel->getByTitre('CDI');
        if (!$typeCDI) {
            Flight::json(['success' => false, 'message' => 'Type de contrat CDI introuvable']);
            return;
        }

        $idContrat = $this->contratTravailModel->insert(
            $typeCDI['id_type_contrat'],
            $id,
            $debut,
            $fin,
            $salaire_base,
            $date_signature
        );

        if ($idContrat) {
            Flight::json(['success' => true, 'message' => 'CDI créé avec succès', 'id_contrat' => $idContrat]);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la création du CDI']);
        }
    }

    public function creerCDD($id) {

        $debut = Flight::request()->query->debut ?? date('Y-m-d');
        $fin = Flight::request()->query->fin ?? null;
        $salaire_base = Flight::request()->query->salaire_base ?? null;
        $date_signature = Flight::request()->query->date_signature ?? null;

        $employe = $this->employeModel->getById($id);
        if (!$employe) {
            Flight::json(['success' => false, 'message' => 'Employé introuvable']);
            return;
        }

        $typeCDD = $this->contratTypeModel->getByTitre('CDD');
        if (!$typeCDD) {
            Flight::json(['success' => false, 'message' => 'Type de contrat CDD introuvable']);
            return;
        }

        if (!$fin) {
            $fin = date('Y-m-d', strtotime("+{$typeCDD['duree_max']} months", strtotime($debut)));
        }

        $idContrat = $this->contratTravailModel->insert(
            $typeCDD['id_type_contrat'],
            $id,
            $debut,
            $fin,
            $salaire_base,
            $date_signature
        );

        if ($idContrat) {
            Flight::json(['success' => true, 'message' => 'CDD créé avec succès', 'id_contrat' => $idContrat]);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la création du CDD']);
        }
    }


}