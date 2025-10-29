<?php

namespace app\controllers\ressourceHumaine\candidat;

use app\models\ressourceHumaine\candidat\CandidatModel;
use app\models\ressourceHumaine\cv\CvModel;
use app\models\ressourceHumaine\cv\DetailCvModel;
use app\models\ressourceHumaine\cv\PostulanceModel;
use Flight;

use app\models\ressourceHumaine\contratEssai\ContratEssaiModel;
use app\models\ressourceHumaine\qcm\QcmModel;
use app\models\ressourceHumaine\resultatCandidat\ResultatCandidatModel;
use app\models\ressourceHumaine\typeResultatCandidat\TypeResultatCandidatModel;

class CandidatController
{
    // Filtrage dynamique des candidats pour le back office
    public function filter()
    {
        $filters = Flight::request()->data->getData();
        // Correction pour compatibilité avec le modèle
        if (isset($filters['diplome']) && is_string($filters['diplome'])) {
            $filters['diplome'] = (int)$filters['diplome'];
        }
        if (isset($filters['ville']) && is_string($filters['ville'])) {
            $filters['ville'] = (int)$filters['ville'];
        }
        if (isset($filters['competences']) && !is_array($filters['competences'])) {
            $filters['competences'] = [$filters['competences']];
        }
        if (isset($filters['profils']) && !is_array($filters['profils'])) {
            $filters['profils'] = [$filters['profils']];
        }
        $diplomeModel = new \app\models\ressourceHumaine\diplome\DiplomeModel();
        $competenceModel = new \app\models\ressourceHumaine\competence\CompetenceModel();
        $candidatModel = new CandidatModel();
        $cvModel = new \app\models\ressourceHumaine\cv\CvModel();
        $contratEssaiModel = new ContratEssaiModel();
        $resultatCandidatModel = new ResultatCandidatModel();
        $typeResultatCandidatModel = new TypeResultatCandidatModel();
        $db = \Flight::db();
        $villes = $db->query('SELECT * FROM ville ORDER BY nom ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $profils = $db->query('SELECT * FROM profil ORDER BY nom ASC')->fetchAll(\PDO::FETCH_ASSOC);

        $diplomes = $diplomeModel->getAll();
        $competences = $competenceModel->getAll();

        // Priorité : si 'eligible' est coché, on filtre uniquement sur les candidats ayant un résultat
        if (!empty($filters['statut']) && $filters['statut'] === 'eligible') {
            $idsEligible = $resultatCandidatModel->getAllCandidatIds();
            if (!empty($idsEligible)) {
                $filters['idsSousContrat'] = $idsEligible;
            } else {
                $filters['idsSousContrat'] = [-1]; // Aucun id, renvoie vide
            }
        } else if (!empty($filters['statut']) && $filters['statut'] === 'sous-contrat') {
            $idsSousContrat = $contratEssaiModel->getAllCandidatIds();
            if (!empty($idsSousContrat)) {
                $filters['idsSousContrat'] = $idsSousContrat;
            } else {
                $filters['idsSousContrat'] = [-1]; // Aucun id, renvoie vide
            }
        }

        $candidats = $candidatModel->getFiltered($filters);

        // Récupérer les photos des candidats via CV
        $photos = [];
        $statuts = [];
        foreach ($candidats as $cand) {
            $id_candidat = $cand['id_candidat'];
            $stmt = $db->prepare('SELECT photo FROM cv WHERE id_candidat = ? ORDER BY id_cv DESC LIMIT 1');
            $stmt->execute([$id_candidat]);
            $photos[$id_candidat] = $stmt->fetchColumn();

            // Statut prioritaire
            // 1. Sous-contrat
            $contrat = $contratEssaiModel->getByCandidat($id_candidat);
            if (!empty($contrat)) {
                $statuts[$id_candidat] = 'Sous-contrat';
                continue;
            }
            // 2. Résultat
            $resultats = $resultatCandidatModel->getByCandidat($id_candidat);
            if (!empty($resultats)) {
                $res = $resultats[0]; // On prend le plus récent si plusieurs
                // Récupérer le type
                $type = $db->prepare('SELECT valeur FROM type_resultat_candidat WHERE id_type_resultat_candidat = ?');
                $type->execute([$res['id_type_resultat_candidat']]);
                $valeur = $type->fetchColumn();
                if ($valeur === 'attente') {
                    $statuts[$id_candidat] = 'Révision';
                } elseif ($valeur === 'refus') {
                    $statuts[$id_candidat] = 'Refusé';
                } else {
                    $statuts[$id_candidat] = 'Archivé';
                }
                continue;
            }
            // 3. Aucun statut
            $statuts[$id_candidat] = 'Archivé';
        }

        Flight::render('ressourceHumaine/back/cv', [
            'diplomes' => $diplomes,
            'competences' => $competences,
            'villes' => $villes,
            'profils' => $profils,
            'candidats' => $candidats,
            'photos' => $photos,
            'statuts' => $statuts,
            'filters' => $filters
        ]);
    }

    public function create()
    {
        // Récupérer les données du formulaire
        $data = Flight::request()->data->getData();
        $cvModel = new CvModel();
        $db = \Flight::db();
        $candidatModel = new CandidatModel();

        // Vérification âge
        $age = $candidatModel->getAge($data['date_naissance'] ?? null);
        $id_annonce_url = '';
        if (!empty($data['id_annonce'])) {
            $id_annonce_url = '&id_annonce=' . urlencode($data['id_annonce']);
        }
        if ($age !== null && $age < 16) {
            Flight::redirect('/candidature?success=0&error=age' . $id_annonce_url);
            return;
        }
        // Vérification numéro de téléphone
        $telephone = $data['telephone'] ?? '';
        if (!preg_match('/^\d+$/', $telephone)) {
            Flight::redirect('/candidature?success=0&error=tel' . $id_annonce_url);
            return;
        }

        // Récupérer l'id_annonce depuis l'URL (GET ou POST) pour trouver le profil
        $id_annonce = $_GET['id_annonce'] ?? ($data['id_annonce'] ?? null);
        $id_profil = 1; // Valeur par défaut si pas d'annonce ou annonce non trouvée
        if ($id_annonce) {
            $stmt = $db->prepare('SELECT id_profil FROM annonce WHERE id_annonce = ?');
            $stmt->execute([$id_annonce]);
            $id_profil_db = $stmt->fetchColumn();
            if ($id_profil_db !== false && $id_profil_db !== null) {
                $id_profil = $id_profil_db;
            }
        }

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
            $id_annonce_url = '';
            if (!empty($data['id_annonce'])) {
                $id_annonce_url = '&id_annonce=' . urlencode($data['id_annonce']);
            }
            Flight::redirect('/candidature?success=0&error=mail' . $id_annonce_url);
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
        if ($genre !== 'M' && $genre !== 'F') {
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
            $genre,
            $data['date_naissance'] ?? null
        );
        $id_cv = $cvModel->insert($id_candidat, $id_profil, $data['photo'] ?? null);

        // Insérer les diplômes dans detail_cv
        $detailCvModel = new DetailCvModel();
        if (!empty($data['diplome'])) {
            $diplomes = is_array($data['diplome']) ? $data['diplome'] : json_decode($data['diplome'], true);
            foreach ($diplomes as $diplome_nom) {
                // Chercher l'id du diplôme
                $stmt = $db->prepare('SELECT id_diplome FROM diplome WHERE nom = ?');
                $stmt->execute([$diplome_nom]);
                $id_diplome = $stmt->fetchColumn();
                if ($id_diplome) {
                    $detailCvModel->insert($id_cv, 'diplome', $id_diplome);
                }
            }
        }

        // Insérer les compétences dans detail_cv
        if (!empty($data['competences'])) {
            $competences = is_array($data['competences']) ? $data['competences'] : json_decode($data['competences'], true);
            foreach ($competences as $competence_nom) {
                // Chercher l'id de la compétence
                $stmt = $db->prepare('SELECT id_competence FROM competence WHERE nom = ?');
                $stmt->execute([$competence_nom]);
                $id_competence = $stmt->fetchColumn();
                if ($id_competence) {
                    $detailCvModel->insert($id_cv, 'competence', $id_competence);
                }
            }
        }

        // Insérer la ville dans detail_cv si présente
        if (!empty($id_ville)) {
            $detailCvModel->insert($id_cv, 'ville', $id_ville);
        }

        $id_annonce_url = '';
        if (!empty($data['id_annonce'])) {
            $id_annonce_url = '&id_annonce=' . urlencode($data['id_annonce']);
        }

        PostulanceModel::insertPostulance($id_cv, $id_annonce);

        $eligible = PostulanceModel::eligibilite($id_annonce, $id_candidat);

        if ($eligible) {        
            $qcm = QcmModel::randomQcm($id_profil);
            Flight::redirect('/interviewQcm?id='.$qcm['id_qcm'].'&id_candidat='.$id_candidat);
        } else {
            Flight::redirect('/candidature?success=1' . $id_annonce_url);
        }
    }

    public function getById($id)
    {
        $candidatModel = new CandidatModel();
        $candidat = $candidatModel->getById($id);
        Flight::render('candidat/show', ['candidat' => $candidat]);
    }

    public function getAll()
    {
        $candidatModel = new CandidatModel();
        $candidats = $candidatModel->getAll();
        Flight::render('candidat/listes', ['candidats' => $candidats]);
    }

    public function update($id)
    {
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
    
    public function delete($id)
    {
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

    public function backOfficeCandidat()
    {
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

        $photos = [];
        $statuts = [];
        foreach ($candidats as $cand) {

            $contratEssaiModel = new ContratEssaiModel();
            $resultatCandidatModel = new ResultatCandidatModel();
            $id_candidat = $cand['id_candidat'];
            $stmt = $db->prepare('SELECT photo FROM cv WHERE id_candidat = ? ORDER BY id_cv DESC LIMIT 1');
            $stmt->execute([$id_candidat]);
            $photos[$id_candidat] = $stmt->fetchColumn();

            // Statut prioritaire
            // 1. Sous-contrat
            $contrat = $contratEssaiModel->getByCandidat($id_candidat);
            if (!empty($contrat)) {
                $statuts[$id_candidat] = 'Sous-contrat';
                continue;
            }
            // 2. Résultat
            $resultats = $resultatCandidatModel->getByCandidat($id_candidat);
            if (!empty($resultats)) {
                $res = $resultats[0]; // On prend le plus récent si plusieurs
                // Récupérer le type
                $type = $db->prepare('SELECT valeur FROM type_resultat_candidat WHERE id_type_resultat_candidat = ?');
                $type->execute([$res['id_type_resultat_candidat']]);
                $valeur = $type->fetchColumn();
                if ($valeur === 'attente') {
                    $statuts[$id_candidat] = 'Révision';
                } elseif ($valeur === 'refus') {
                    $statuts[$id_candidat] = 'Refusé';
                } else {
                    $statuts[$id_candidat] = 'Archivé';
                }
                continue;
            }
            // 3. Aucun statut
            $statuts[$id_candidat] = 'Archivé';
        }


        Flight::render('ressourceHumaine/back/cv', [
            'diplomes' => $diplomes,
            'competences' => $competences,
            'villes' => $villes,
            'profils' => $profils,
            'statuts' => $statuts,
            'photos' => $photos,
            'candidats' => $candidats
        ]);
    }

    public function candidatDetail($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID du candidat manquant"]);
            return;
        }

        $model = new CandidatModel();
        $data = $model->cvAPI($id);

        if (!$data) {
            http_response_code(404);
            echo json_encode(["error" => "Candidat non trouvé"]);
            return;
        }

        // Retourner les données en JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function candidatAllDetail() {
        echo "caca";
        // $model = new CandidatModel();
        // $data = $model->cvApiAll();

        // if (!$data) {
        //     http_response_code(404);
        //     echo json_encode(["error" => "pas de candidat trouvé"]);
        //     return;
        // }

        // header('Content-Type: application/json');
        // echo json_encode($data);
    }

    public function exportCvToExcel($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID du candidat manquant"]);
            return;
        }

        $candidatModel = new CandidatModel();
        $data = $candidatModel->cvAPI($id);
        if (!$data) {
            http_response_code(404);
            echo json_encode(["error" => "Candidat non trouvé"]);
            return;
        }

        $candidatModel->exportCvToExcel($data);
    }

    public function detailCv()
    {
        Flight::render('ressourceHumaine/back/cvDetail');
    }

}
