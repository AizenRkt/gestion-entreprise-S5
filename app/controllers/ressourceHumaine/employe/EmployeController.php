<?php
namespace app\controllers\ressourceHumaine\employe;

use app\models\ressourceHumaine\employe\EmployeModel;
use app\models\ressourceHumaine\contratTravail\ContratTravailModel;
use app\models\ressourceHumaine\candidat\CandidatModel;

use Flight;
use PDO;
use Exception;

class EmployeController {
    public function listEmployes() {
        $model = new EmployeModel();
        $employes = $model->getAllEmployes();
        $postes = $model->getAllPostes(); // Récupérer les postes
        Flight::render('ressourceHumaine/back/employe/listemploye', [
            'employes' => $employes,
            'postes' => $postes // Passer les postes à la vue
        ]);
    }

    public function updateEmploye() {
        if (Flight::request()->method == 'POST') {
            $model = new EmployeModel();
            $data = Flight::request()->data;
            
            $id = $data['id_employe'];
            $nom = $data['nom'];
            $prenom = $data['prenom'];
            $email = $data['email'];
            $telephone = $data['telephone'];
            $genre = $data['genre'];
            $date_embauche = $data['date_embauche'];
            $activite = $data['activite'];
            $id_poste = $data['id_poste']; 

            $model->updateEmploye($id, $nom, $prenom, $email, $telephone, $genre, $date_embauche, $activite, $id_poste);

            Flight::redirect('/employes');
        }
    }

    public function ficheEmploye() {
        Flight::render('ressourceHumaine/back/employe/ficheEmploye');
    }

    public function statutEmpNow($id) {
        if (!$id) {
            Flight::json([
                "success" => false,
                "message" => "Paramètre id_employe manquant"
            ], 400);
            return;
        }

        $infos = EmployeModel::getEmployeStatutNow($id);

        if ($infos === null) {
            Flight::json([
                "success" => false,
                "message" => "Erreur lors de la récupération des informations"
            ], 500);
            return;
        }

        
        Flight::json([
            "success" => true,
            "data" => $infos
        ]);
    }

    public function statutEmpHistorique($id) {
        if (!$id) {
            Flight::json([
                "success" => false,
                "message" => "Paramètre id_employe manquant"
            ], 400);
            return;
        }

        $infos = EmployeModel::getEmployeHistoriqueStatut($id);

        if ($infos === null) {
            Flight::json([
                "success" => false,
                "message" => "Erreur lors de la récupération des informations"
            ], 500);
            return;
        }
        
        Flight::json([
            "success" => true,
            "data" => $infos
        ]);
    }
    
    // recruter un sans passer par une annonce, etc
    public function recruterEmploye() {
        try {

            $req = Flight::request()->data;

            $nom             = $req->nom ?? null;
            $prenom          = $req->prenom ?? null;
            $email           = $req->email ?? null;
            $telephone       = $req->telephone ?? null;
            $genre           = $req->genre ?? null;
            $date_embauche   = $req->date_embauche ?? date('Y-m-d');
            $activite        = $req->activite ?? 1;
            $id_poste        = $req->id_poste ?? null;
            $date_naissance  = $req->date_naissance ?? null;

            $type_contrat    = strtoupper($req->type_contrat ?? "");
            $date_debut      = $req->date_debut ?? date('Y-m-d');
            $date_fin        = $req->date_fin ?? null;
            $salaire_base    = $req->salaire_base ?? null;
            $date_signature  = $req->date_signature ?? null;

            if (!$nom || !$prenom || !$email || !$id_poste || !$type_contrat || !$date_naissance) {
                return Flight::json([
                    'success' => false,
                    'message' => 'Paramètres manquants'
                ], 400);
            }

            $candidatModel = new CandidatModel();

            $id_candidat = $candidatModel->insert($nom, $prenom, $email, $telephone, $genre, $date_naissance);

            $employeInfo = EmployeModel::createEmploye(
                $nom,
                $prenom,
                $email,
                $telephone,
                $genre,
                $date_embauche,
                $activite,
                $id_poste,
                $id_candidat
            );

            $contratTravailModel = new ContratTravailModel();
            $id_contrat = null;

            if ($type_contrat === "CDI") {

                $id_contrat = $contratTravailModel->creerCDI(
                    $employeInfo['id_employe'],
                    $date_debut,
                    null,
                    $salaire_base,
                    $date_signature,
                    $id_poste
                );

            } elseif ($type_contrat === "CDD") {

                $id_contrat = $contratTravailModel->creerCDD(
                    $employeInfo['id_employe'],
                    $date_debut,
                    $date_fin,
                    $salaire_base,
                    $date_signature,
                    $id_poste
                );

            } else {
                throw new Exception("Type de contrat invalide : $type_contrat");
            }

            if (!$id_contrat) {
                throw new Exception("Erreur lors de la création du contrat");
            }

            $id_contratEmployeStatut = EmployeModel::insertContratEmployeStatut(
                $id_contrat,
                $employeInfo['id_statut']
            );

            if (!$id_contratEmployeStatut) {
                throw new Exception("Erreur lors de la liaison employé/statut");
            }

            return Flight::json([
                'success' => true,
                'message' => 'Employé recruté avec succès',
                'id_employe' => $employeInfo['id_employe'],
                'id_contrat' => $id_contrat,
                'id_contrat_employe_statut' => $id_contratEmployeStatut
            ]);

        } catch (Exception $e) {

            return Flight::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // changer le poste d'un employe
    public function changerPosteEmploye($id) {
        $req = Flight::request()->data;

        $id_poste_new = $req->id_poste ?? null;

        if(!$id_poste_new) {
            return Flight::json([
                'success' => false,
                'message' => 'vous devez choisir un poste'
            ], 400);
        }

        // crée un statut pour quitter le poste (employe_statut)
        $infos = EmployeModel::getEmployeStatutNow($id);

        if($id_poste_new == $infos['id_poste']) {
            return Flight::json([
                'success' => false,
                'message' => 'vous devez choisir un nouveau poste'
            ], 400);
        }

        EmployeModel::insertEmployeStatut($id, $infos['id_poste'], 0);

        //crée un statut pour le nouveau poste 
        $id_new_employe_statut =  EmployeModel::insertEmployeStatut($id, $id_poste_new, 1);

        // crée un nouveau contrat (contrat_travail)
        $type_contrat    = strtoupper($req->type_contrat ?? "");
        $date_debut      = $req->date_debut ?? date('Y-m-d');
        $date_fin        = $req->date_fin ?? null;
        $salaire_base    = $req->salaire_base ?? null;
        $date_signature  = $req->date_signature ?? null;
        
        $contratTravailModel = new ContratTravailModel();
        $id_contrat = null;

        if ($type_contrat === "CDI") {

            $id_contrat = $contratTravailModel->creerCDI(
                $id,
                $date_debut,
                null,
                $salaire_base,
                $date_signature,
                $id_poste_new
            );

        } elseif ($type_contrat === "CDD") {

            $id_contrat = $contratTravailModel->creerCDD(
                $id,
                $date_debut,
                $date_fin,
                $salaire_base,
                $date_signature,
                $id_poste_new
            );

        } else {
            throw new Exception("Type de contrat invalide : $type_contrat");
        }

        if (!$id_contrat) {
            throw new Exception("Erreur lors de la création du contrat");
        }

        // crée un statut (contrat_employe_statut)
        $id_contratEmployeStatut = EmployeModel::insertContratEmployeStatut(
            $id_contrat,
            $id_new_employe_statut
        );

        if (!$id_contratEmployeStatut) {
            throw new Exception("Erreur lors de la liaison employé/statut");
        }

        return Flight::json([
            'success' => true,
            'message' => 'le poste de employé a été changé avec succès',
            'id_employe' => $id,
            'id_contrat' => $id_contrat,
            'id_contrat_employe_statut' => $id_contratEmployeStatut
        ]);
    }

    public function demissionEmploye() {
        // crée un statut (employe_statut)
    }

}