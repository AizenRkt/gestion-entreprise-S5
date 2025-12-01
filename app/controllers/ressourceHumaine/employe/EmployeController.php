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
        $filters = [];
        
        if (Flight::request()->method == 'POST') {
            $data = Flight::request()->data;
            if (!empty($data['genre'])) {
                $filters['genre'] = $data['genre'];
            }
            if (!empty($data['date_debut'])) {
                $filters['date_debut'] = $data['date_debut'];
            }
            if (!empty($data['date_fin'])) {
                $filters['date_fin'] = $data['date_fin'];
            }
            if (!empty($data['id_dept'])) {
                $filters['id_dept'] = $data['id_dept'];
            }
            if (!empty($data['id_service'])) {
                $filters['id_service'] = $data['id_service'];
            }
        }
        
        $employes = $model->getAllEmployes($filters);
        $postes = $model->getAllPostes();
        $departements = $model->getAllDepartements();
        $services = $model->getAllServices();
        
        Flight::render('ressourceHumaine/back/employe/listemploye', [
            'employes' => $employes,
            'postes' => $postes,
            'departements' => $departements,
            'services' => $services
        ]);
    }

    public function alertesEmployes() {
        $model = new EmployeModel();
        $alertes = $model->getAlertes();
        
        Flight::render('ressourceHumaine/back/employe/AlerteEmploye', [
            'alertes' => $alertes
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
    public function getUserProfile() {
        // Debug: Afficher les données de session
        error_log("Session user data: " . print_r($_SESSION['user'] ?? 'No session', true));
        
        if (!isset($_SESSION['user']['id_employe'])) {
            error_log("No id_employe in session, redirecting to /log");
            Flight::redirect('/log');
            return;
        }

        $model = new EmployeModel();
        $employe = $model->getById($_SESSION['user']['id_employe']);
        
        // Debug: Afficher les données récupérées
        error_log("Employe data from DB: " . print_r($employe, true));
        
        if (!$employe) {
            error_log("No employe found with id: " . $_SESSION['user']['id_employe']);
            Flight::error('Employé non trouvé');
            return;
        }

        // Debug: Vérifier que les données sont passées à la vue
        error_log("Passing employe data to view");
        
        Flight::render('auth/user/parametre', [
            'employe' => $employe
        ]);
    }

    public function updateUserProfile() {
        if (Flight::request()->method == 'POST' && isset($_SESSION['user']['id_employe'])) {
            $model = new EmployeModel();
            $data = Flight::request()->data;
            
            $id = $_SESSION['user']['id_employe'];
            $nom = $data['nom'];
            $prenom = $data['prenom'];
            $email = $data['email'];
            $telephone = $data['telephone'];
            $genre = $data['genre'];

            // Mettre à jour seulement les informations de base (pas le poste ou l'activité)
            $db = Flight::db();
            $sql = "UPDATE employe SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, genre = :genre WHERE id_employe = :id_employe";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':genre' => $genre,
                ':id_employe' => $id
            ]);

            if ($success) {
                // Mettre à jour la session
                $_SESSION['user']['username'] = $data['username'];
                Flight::json(['success' => true, 'message' => 'Profil mis à jour avec succès']);
            } else {
                Flight::json(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
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

            $id_contratEmployeStatut = null;

            try {
                $id_contratEmployeStatut = EmployeModel::insertContratEmployeStatut(
                    $id_contrat,
                    $employeInfo['id_statut']
                );
            } catch (\PDOException $e) {
                throw new Exception("Erreur lors de la liaison employé/statut : " . $e->getMessage());
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