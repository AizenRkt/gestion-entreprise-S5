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
}