<?php
namespace app\controllers\ressourceHumaine\employe;
use app\models\ressourceHumaine\employe\EmployeModel;
use Flight;
use PDO;

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
}