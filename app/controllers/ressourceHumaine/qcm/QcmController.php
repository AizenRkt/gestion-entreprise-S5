<?php

namespace app\controllers\ressourceHumaine\qcm;
use app\models\ressourceHumaine\qcm\QcmModel\QcmModel;
use Flight;

class QcmController {

    public function create() {
        $data = Flight::request()->data;
        $qcmModel = new QcmModel();
        $success = $qcmModel->insert($data->id_annonce, $data->titre, $data->note_max);
        $message = (strpos($success, 'succès') !== false) ? "Création réussie." : "Échec de création.";
        Flight::render('qcm/create', ['message' => $message]);
    }

    public function getById($id) {
        $qcmModel = new QcmModel();
        $qcm = $qcmModel->getById($id);
        Flight::render('qcm/show', ['qcm' => $qcm]);
    }

    public function getAll() {
        $qcmModel = new QcmModel();
        $qcms = $qcmModel->getAll();
        Flight::render('qcm/listes', ['qcms' => $qcms]);
    }

    public function update($id) {
        $data = Flight::request()->data;
        $qcmModel = new QcmModel();
        $success = $qcmModel->update($id, $data->id_annonce, $data->titre, $data->note_max);
        $message = (strpos($success, 'réussie') !== false) ? "Mise à jour réussie." : "Échec de mise à jour.";

        $qcm = $qcmModel->getById($id);
        Flight::render('qcm/edit', ['qcm' => $qcm, 'message' => $message]);
    }

    public function delete($id) {
        $qcmModel = new QcmModel();
        $success = $qcmModel->delete($id);
        $message = (strpos($success, 'réussie') !== false) ? "Suppression réussie." : "Échec de suppression.";

        $qcms = $qcmModel->getAll();
        Flight::render('qcm/listes', ['qcms' => $qcms, 'message' => $message]);
    }
}
?>
