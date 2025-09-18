<?php

namespace app\controllers\candidat;
use app\models\ressourceHumaine\candidat\CandidatModel;
use Flight;

class CandidatController {

    public function create() {
        $data = Flight::request()->data;
        $candidatModel = new CandidatModel();
        $success = $candidatModel->insert(
            $data->nom,
            $data->prenom,
            $data->email,
            $data->telephone,
            $data->genre
        );
        $message = (strpos($success, 'succès') !== false)
            ? "Candidat ajouté avec succès."
            : "Échec de l'ajout.";
        Flight::render('candidat/create', ['message' => $message]);
    }

    public function getById($id) {
        $candidatModel = new CandidatModel();
        $candidat = $candidatModel->getById($id);
        Flight::render('candidat/show', ['candidat' => $candidat]);
    }

    public function getAll() {
        $candidatModel = new CandidatModel();
        $candidats = $candidatModel->getAll();
        Flight::render('candidat/listes', ['candidats' => $candidats]);
    }

    public function update($id) {
        $data = Flight::request()->data;
        $candidatModel = new CandidatModel();
        $success = $candidatModel->update(
            $id,
            $data->nom,
            $data->prenom,
            $data->email,
            $data->telephone,
            $data->genre
        );
        $message = (strpos($success, 'réussie') !== false)
            ? "Mise à jour réussie."
            : "Échec de mise à jour.";

        $candidat = $candidatModel->getById($id);
        Flight::render('candidat/edit', [
            'candidat' => $candidat,
            'message' => $message
        ]);
    }

    public function delete($id) {
        $candidatModel = new CandidatModel();
        $success = $candidatModel->delete($id);
        $message = (strpos($success, 'réussie') !== false)
            ? "Suppression réussie."
            : "Échec de suppression.";

        $candidats = $candidatModel->getAll();
        Flight::render('candidat/listes', [
            'candidats' => $candidats,
            'message' => $message
        ]);
    }
}
?>
