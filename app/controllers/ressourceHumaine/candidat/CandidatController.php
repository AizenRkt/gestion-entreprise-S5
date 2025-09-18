<?php

namespace app\controllers\ressourceHumaine\candidat;
use app\models\ressourceHumaine\candidat\CandidatModel;
use app\models\ressourceHumaine\cv\CvModel;
use app\models\ressourceHumaine\cv\DetailCvModel;
use Flight;

class CandidatController {



    public function create() {
        // Récupérer les données du formulaire
        $data = Flight::request()->data->getData();
        // Vérification unicité email pour le même profil
        $cvModel = new CvModel();
        $id_profil = 1; // profil forcé à 1
        $db = \Flight::db();
        // Récupérer tous les CV avec ce profil
        $stmt = $db->prepare('SELECT id_candidat FROM cv WHERE id_profil = ?');
        $stmt->execute([$id_profil]);
        $candidatsProfil = $stmt->fetchAll();
        $emailToCheck = $data['email'] ?? '';
        $emailExists = false;
        foreach ($candidatsProfil as $row) {
            $stmt2 = $db->prepare('SELECT email FROM candidat WHERE id_candidat = ?');
            $stmt2->execute([$row['id_candidat']]);
            $email = $stmt2->fetchColumn();
            if ($email === $emailToCheck) {
                $emailExists = true;
                break;
            }
        }
        if ($emailExists) {
            // Rediriger avec message d'erreur
            Flight::redirect('/candidature?success=0&error=mail');
            return;
        }

        // Gérer le téléchargement de la photo
        if (isset(Flight::request()->files['photo']) && Flight::request()->files['photo']['error'] == UPLOAD_ERR_OK) {
            $photo = Flight::request()->files['photo'];
            $extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('photo_', true) . '.' . $extension;
            // Déplacement du fichier (optionnel, mais on n'enregistre que le nom)
            $uploadDir = 'public/uploads/photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadPath = $uploadDir . $uniqueName;
            move_uploaded_file($photo['tmp_name'], $uploadPath);
            $data['photo'] = $uniqueName; // Stocker uniquement le nom du fichier
        } else {
            $data['photo'] = null;
        }

        // Encoder les champs multivalués (diplômes, compétences) en JSON
        $data['diplome'] = isset($data['diplome']) ? json_encode($data['diplome']) : json_encode([]);
        $data['competences'] = isset($data['competences']) ? json_encode($data['competences']) : json_encode([]);

        // Insérer les données via le modèle
        $candidatModel = new CandidatModel();
        $id_candidat = $candidatModel->insert(
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['email'] ?? '',
            $data['telephone'] ?? '',
            $data['genre'] ?? ''
        );
        $id_cv = $cvModel->insert($id_candidat, $id_profil, $data['photo'] ?? null);

        // Insérer les diplômes dans detail_cv
        $detailCvModel = new DetailCvModel();
        if (!empty($data['diplome'])) {
            $diplomes = is_array($data['diplome']) ? $data['diplome'] : json_decode($data['diplome'], true);
            foreach ($diplomes as $diplomeNom) {
                // Récupérer l'id du diplôme par son nom
                $stmt = $db->prepare('SELECT id_diplome FROM diplome WHERE nom = ?');
                $stmt->execute([$diplomeNom]);
                $row = $stmt->fetch();
                if ($row) {
                    $detailCvModel->insert($id_cv, 'diplome', $row['id_diplome']);
                }
            }
        }

        // Insérer les compétences dans detail_cv
        if (!empty($data['competences'])) {
            $competences = is_array($data['competences']) ? $data['competences'] : json_decode($data['competences'], true);
            foreach ($competences as $competenceNom) {
                // Récupérer l'id de la compétence par son nom
                $stmt = $db->prepare('SELECT id_competence FROM competence WHERE nom = ?');
                $stmt->execute([$competenceNom]);
                $row = $stmt->fetch();
                if ($row) {
                    $detailCvModel->insert($id_cv, 'competence', $row['id_competence']);
                }
            }
        }

        // Rediriger vers la page de candidature après insertion avec message succès
        Flight::redirect('/candidature?success=1');
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
