<?php
namespace app\controllers\ressourceHumaine\employe;
use app\models\ressourceHumaine\employe\EmployeModel;
use Flight;
use PDO;

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
}