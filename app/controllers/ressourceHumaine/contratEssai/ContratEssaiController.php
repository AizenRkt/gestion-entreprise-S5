<?php
namespace app\controllers\ressourceHumaine\contratEssai;

use app\models\ressourceHumaine\contratEssai\ContratEssaiModel;
use Flight;

class ContratEssaiController {

    public function create() {
        $data = Flight::request()->data;
        $contratModel = new ContratEssaiModel();
        $success = $contratModel->insert($data->id_candidat, $data->debut, $data->fin);
        $message = (strpos($success, 'succès') !== false) ? "Création réussie." : "Échec de création.";
        Flight::render('contratEssai/create', ['message' => $message]);
    }

    public function getById($id) {
        $contratModel = new ContratEssaiModel();
        $contrat = $contratModel->getById($id);
        Flight::render('contratEssai/show', ['contrat' => $contrat]);
    }

    public function getAll() {
        $contratModel = new ContratEssaiModel();
        $contrats = $contratModel->getAll();
        Flight::render('contratEssai/listes', ['contrats' => $contrats]);
    }

    public function update($id) {
        $data = Flight::request()->data;
        $contratModel = new ContratEssaiModel();
        $success = $contratModel->update($id, $data->id_candidat, $data->debut, $data->fin);
        $message = (strpos($success, 'réussie') !== false) ? "Mise à jour réussie." : "Échec de mise à jour.";

        $contrat = $contratModel->getById($id);
        Flight::render('contratEssai/edit', ['contrat' => $contrat, 'message' => $message]);
    }

    public function delete($id) {
        $contratModel = new ContratEssaiModel();
        $success = $contratModel->delete($id);
        $message = (strpos($success, 'réussie') !== false) ? "Suppression réussie." : "Échec de suppression.";

        $contrats = $contratModel->getAll();
        Flight::render('contratEssai/listes', ['contrats' => $contrats, 'message' => $message]);
    }


    public function generatePdf($id) {
        $contratModel = new ContratEssaiModel();
        $contratModel->generatePdf($id);
    }
}
