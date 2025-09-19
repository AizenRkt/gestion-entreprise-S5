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
        $cvModel = new CvModel();
        $id_profil = 1; // profil forcé à 1
        $db = \Flight::db();

        // Vérification unicité email pour le même profil
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
            Flight::redirect('/candidature?success=0&error=mail');
            return;
        }

        // Gérer le téléchargement de la photo
        if (isset(Flight::request()->files['photo']) && Flight::request()->files['photo']['error'] == UPLOAD_ERR_OK) {
            $photo = Flight::request()->files['photo'];
            $extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('photo_', true) . '.' . $extension;
            $uploadDir = 'public/uploads/photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadPath = $uploadDir . $uniqueName;
            move_uploaded_file($photo['tmp_name'], $uploadPath);
            $data['photo'] = $uniqueName;
        } else {
            $data['photo'] = null;
        }

        // Correction genre (H/F)
        $genre = strtoupper(substr($data['genre'] ?? '', 0, 1));
        if ($genre !== 'H' && $genre !== 'F') {
            $genre = null;
        }

    // Ville comme id (depuis le select)
    $id_ville = $data['ville'] ?? null;

        // Insérer le candidat
        $candidatModel = new CandidatModel();
        $id_candidat = $candidatModel->insert(
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['email'] ?? '',
            $data['telephone'] ?? '',
            $genre
        );
        $id_cv = $cvModel->insert($id_candidat, $id_profil, $data['photo'] ?? null);

        // Insérer les diplômes dans detail_cv
        $detailCvModel = new DetailCvModel();
        if (!empty($data['diplome'])) {
            $diplomes = is_array($data['diplome']) ? $data['diplome'] : json_decode($data['diplome'], true);
            foreach ($diplomes as $id_diplome) {
                $detailCvModel->insert($id_cv, 'diplome', $id_diplome);
            }
        }

        // Insérer les compétences dans detail_cv
        if (!empty($data['competences'])) {
            $competences = is_array($data['competences']) ? $data['competences'] : json_decode($data['competences'], true);
            foreach ($competences as $id_competence) {
                $detailCvModel->insert($id_cv, 'competence', $id_competence);
            }
        }

        // Insérer la ville dans detail_cv si présente
        if (!empty($id_ville)) {
            $detailCvModel->insert($id_cv, 'ville', $id_ville);
        }

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
    public function backOfficeCandidat(){
        // Charger les données dynamiques
        $diplomeModel = new \app\models\ressourceHumaine\diplome\DiplomeModel();
        $competenceModel = new \app\models\ressourceHumaine\competence\CompetenceModel();
        $candidatModel = new \app\models\ressourceHumaine\candidat\CandidatModel();
        $db = \Flight::db();
        $villes = $db->query('SELECT * FROM ville ORDER BY nom ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $profils = $db->query('SELECT * FROM profil ORDER BY nom ASC')->fetchAll(\PDO::FETCH_ASSOC);

        $diplomes = $diplomeModel->getAll();
        $competences = $competenceModel->getAll();
        $candidats = $candidatModel->getAll();

        Flight::render('ressourceHumaine/back/cv', [
            'diplomes' => $diplomes,
            'competences' => $competences,
            'villes' => $villes,
            'profils' => $profils,
            'candidats' => $candidats
        ]);
    }
}
?>
