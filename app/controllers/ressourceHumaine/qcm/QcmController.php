<?php

namespace app\controllers\ressourceHumaine\qcm;

use app\models\ressourceHumaine\qcm\QcmModel;
use app\models\ressourceHumaine\qcm\QuestionModel;
use app\models\ressourceHumaine\ProfilModel;
use app\models\ressourceHumaine\scoring\ScoringModel;
use Exception;

use Flight;

class QcmController {

    public function seeAllQcm() {
        Flight::render('ressourceHumaine/back/qcm/qcmList');
    }

    public function singleQcm() {
        Flight::render('ressourceHumaine/back/qcm/qcmSinglePage');
    }

    public function interviewQcm() {
        Flight::render('ressourceHumaine/back/qcm/qcmInterview');
    }

    public function createQcm() {
        $profil = ProfilModel::getAll();
        Flight::render('ressourceHumaine/back/qcm/qcmCreate', ['profil' => $profil]);
    }

    public function createQuestion() {
        Flight::render('ressourceHumaine/back/qcm/questionCreate');
    }

    public static function getAll() {
        try {
            $qcm = QcmModel::getAll();
            Flight::json(['success' => true, 'data' => $qcm]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getById($id_qcm) {
        try {
            $qcm = QcmModel::getById($id_qcm);

            if ($qcm) {
                Flight::json(['success' => true, 'data' => $qcm]);
            } else {
                Flight::json(['success' => false, 'message' => 'QCM introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function delete($id_qcm) {
        try {
            $deleted = QcmModel::delete($id_qcm);

            if ($deleted) {
                Flight::json([
                    'success' => true,
                    'message' => "Le QCM #$id_qcm a été supprimé avec succès."
                ]);
            } else {
                Flight::json([
                    'success' => false,
                    'message' => "Impossible de supprimer le QCM #$id_qcm."
                ], 500);
            }

        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => "Erreur : " . $e->getMessage()
            ], 500);
        }
    }

    public static function getAllQuestion() {
        try {
            $questions = QuestionModel::getAll();
            Flight::json(['success' => true, 'data' => $questions]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getByIdQuestion($id_question) {
        try {
            $question = QuestionModel::getById($id_question);

            if ($question) {
                Flight::json(['success' => true, 'data' => $question]);
            } else {
                Flight::json(['success' => false, 'message' => 'Question introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteQuestion($id_question) {
        try {
            $deleted = QuestionModel::delete($id_question);

            if ($deleted) {
                Flight::json([
                    'success' => true,
                    'message' => "La question #$id_question a été supprimée avec succès."
                ]);
            } else {
                Flight::json([
                    'success' => false,
                    'message' => "Impossible de supprimer la question #$id_question."
                ], 500);
            }

        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => "Erreur : " . $e->getMessage()
            ], 500);
        }
    }

    public static function addQuestion() {
        try {
            $data = Flight::request()->data->getData();

            if (empty($data['enonce']) || empty($data['reponses'])) {
                Flight::json(['success' => false, 'message' => 'Paramètres manquants'], 400);
                return;
            }

            // Appel au modèle
            $idQuestion = QuestionModel::create($data['enonce'], $data['reponses']);

            Flight::json([
                'success' => true,
                'message' => 'Question ajoutée avec succès',
                'id_question' => $idQuestion
            ]);

        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function searchQuestion() {
        try {
            $term = Flight::request()->query['q'];
            if (empty($term)) {
                Flight::json(['success' => false, 'message' => 'Veuillez entrer un terme de recherche'], 400);
                return;
            }

            $results = QuestionModel::search($term);

            Flight::json([
                'success' => true,
                'data' => $results
            ]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function createQcmApi() {
        $data = json_decode(Flight::request()->getBody(), true);

        $id_profil = intval($data['id_profil'] ?? 0);
        $titre = trim($data['titre'] ?? '');
        $note_max = floatval($data['note_max'] ?? 10);
        $questions = $data['questions'] ?? []; 

        if (!$id_profil || !$titre || empty($questions)) {
            Flight::json([
                'success' => false,
                'message' => 'Profil, titre et au moins une question sont requis.'
            ]);
            return;
        }

        try {
            $id_qcm = QcmModel::create($id_profil, $titre, $note_max, $questions);

            Flight::json([
                'success' => true,
                'message' => 'QCM créé avec succès',
                'id_qcm' => $id_qcm
            ]);

            Flight::redirect('/createQcm');

        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de la création du QCM : ' . $e->getMessage()
            ]);
        }
    }

    public function scoringQcm() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['id_candidat'], $data['id_qcm'], $data['score'])) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $id_candidat = (int) $data['id_candidat'];
        $id_qcm = (int) $data['id_qcm'];
        $score = (float) $data['score'];

        $id_typeScoring = 1; 

        try {
            ScoringModel::insertScore($id_candidat, $id_typeScoring, $score);
            echo json_encode(['success' => true, 'score' => $score]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
}

}
?>
