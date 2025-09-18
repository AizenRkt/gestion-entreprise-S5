<?php

namespace app\controllers\ressourceHumaine\candidat;
use app\models\ressourceHumaine\candidat\CandidatModel;
use Flight;

class CandidatController {

    public function annonce() {
        Flight::render('ressourceHumaine/annonce');

    }


    public function create() {
        // Récupérer les données du formulaire
        $data = Flight::request()->data->getData();

        // Gérer le téléchargement de la photo
        if (isset(Flight::request()->files['photo']) && Flight::request()->files['photo']['error'] == UPLOAD_ERR_OK) {
            $photo = Flight::request()->files['photo'];
            $uploadDir = 'public/uploads/photos/';
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Générer un nom de fichier unique
            $extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('photo_', true) . '.' . $extension;
            $uploadPath = $uploadDir . $uniqueName;

            // Déplacer le fichier
            if (move_uploaded_file($photo['tmp_name'], $uploadPath)) {
                $data['photo'] = '/' . $uploadPath; // Stocker le chemin relatif
            } else {
                $data['photo'] = null; // Gérer l'échec du téléchargement
            }
        } else {
            $data['photo'] = null;
        }

        // Encoder les champs multivalués (diplômes, compétences) en JSON
        $data['diplome'] = isset($data['diplome']) ? json_encode($data['diplome']) : json_encode([]);
        $data['competences'] = isset($data['competences']) ? json_encode($data['competences']) : json_encode([]);

        // Insérer les données via le modèle
        $candidatModel = new CandidatModel();
        $message = $candidatModel->insert(
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['email'] ?? '',
            $data['telephone'] ?? '',
            $data['genre'] ?? ''
        );

        // Afficher un message de succès ou d'erreur
        Flight::render('ressourceHumaine/candidature', ['message' => $message]);
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
