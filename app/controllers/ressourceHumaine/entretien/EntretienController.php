<?php

namespace app\controllers\ressourceHumaine\entretien;

use app\models\ressourceHumaine\entretien\Entretien;
use Flight;
use PDO;

class EntretienController {

    public function __construct() {
        // Démarrer la session si ce n'est pas déjà fait
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 📌 Liste des entretiens avec planning
    public function getAll() {
        $entretien = new Entretien();
        $data = $entretien->getAll();

        Flight::render('ressourceHumaine/back/planning2', [
            'entretiens' => $data
        ]);
    }

    // 📌 API pour récupérer les entretiens d'un mois donné (AJAX)
    public function getEntretiensByMonth() {
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('n');
        
        $entretien = new Entretien();
        $entretiens = $entretien->getByMonth($year, $month);
        
        header('Content-Type: application/json');
        echo json_encode($entretiens);
        exit;
    }

    // 📌 Formulaire de création
    public function formCreate() {
        // Récupérer la liste des candidats pour le formulaire
        $db = Flight::db();
        $stmt = $db->query("SELECT id_candidat, nom, prenom, email FROM candidat ORDER BY nom, prenom");
        $candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        Flight::render('ressourceHumaine/back/orgaEntretien', [
            'candidats' => $candidats
        ]);
    }

    // 📌 Création d'un entretien
    public function create() {
        $id_candidat = $_POST['candidat_id'] ?? null;
        $date        = $_POST['date_entretien'] ?? null;
        $heure       = $_POST['heure_entretien'] ?? null;
        $duree       = $_POST['duree_entretien'] ?? null;
        $id_user     = $_SESSION['id_user'] ?? null;

        if ($id_candidat && $date && $heure && $duree) {
            $datetime = $date . " " . $heure . ":00";

            $entretien = new Entretien();
            // Correction de l'ordre des paramètres
            $result = $entretien->create($id_candidat, $datetime, $id_user, $duree);

            // redirection vers le planning
            Flight::redirect('/planning');
        } else {
            Flight::render('errors/400', ['message' => 'Champs manquants']);
        }
    }

    // 📌 Suppression
    public function delete($id) {
        $entretien = new Entretien();
        $entretien->delete($id);

        Flight::redirect('/planning');
    }
    public function getEntretiensByDay() {
    $date = $_GET['date'] ?? date('Y-m-d');
    
    $entretien = new Entretien();
    $entretiens = $entretien->getByDay($date);
    
    header('Content-Type: application/json');
    echo json_encode($entretiens);
    exit;
}
}