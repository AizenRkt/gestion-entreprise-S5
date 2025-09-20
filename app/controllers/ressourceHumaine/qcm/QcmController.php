<?php

namespace app\controllers\ressourceHumaine\qcm;

use app\models\ressourceHumaine\qcm\QcmModel;
use app\models\ressourceHumaine\qcm\QuestionModel;
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
        Flight::render('ressourceHumaine/back/qcm/qcmCreate');
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

}
?>
