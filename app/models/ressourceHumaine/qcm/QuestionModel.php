<?php
namespace app\models\ressourceHumaine\qcm;

use Flight;
use PDO;

class QuestionModel {

    public static function getAll() {
        try {
            $db = Flight::db();

            $sql = "
                SELECT 
                    q.id_question,
                    q.enonce AS question,
                    r.id_reponse,
                    r.texte AS reponse,
                    r.est_correcte
                FROM question q
                JOIN reponse r ON q.id_question = r.id_question
                ORDER BY q.id_question, r.id_reponse;
            ";

            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $questions = [];
            foreach ($rows as $row) {
                $id_question = $row['id_question'];

                if (!isset($questions[$id_question])) {
                    $questions[$id_question] = [
                        'id_question' => $id_question,
                        'enonce' => $row['question'],
                        'reponse' => []
                    ];
                }

                $questions[$id_question]['reponse'][] = [
                    'id_reponse' => $row['id_reponse'],
                    'reponse' => $row['reponse'],
                    'est_correcte' => (bool)$row['est_correcte']
                ];
            }

            return array_values($questions);

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public static function getById($id_question) {
        try {
            $db = Flight::db();

            $sql = "
                SELECT 
                    q.id_question,
                    q.enonce AS question,
                    r.id_reponse,
                    r.texte AS reponse,
                    r.est_correcte
                FROM question q
                JOIN reponse r ON q.id_question = r.id_question
                WHERE q.id_question = :id_question
                ORDER BY r.id_reponse;
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id_question' => $id_question]);

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return null;
            }

            $question = [
                'id_question' => $rows[0]['id_question'],
                'enonce' => $rows[0]['question'],
                'reponses' => []
            ];

            foreach ($rows as $row) {
                $question['reponses'][] = [
                    'id_reponse' => $row['id_reponse'],
                    'texte' => $row['reponse'],
                    'est_correcte' => (bool)$row['est_correcte']
                ];
            }

            return $question;

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public static function insert($enonce) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO question (enonce) VALUES (:enonce)");
            $stmt->execute([':enonce' => $enonce]);
            return "Question insérée avec succès.";
        } catch (\PDOException $e) {
            return "Erreur d'insertion : " . $e->getMessage();
        }
    }

    public static function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM question WHERE id_question = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }

    public static function create($enonce, $reponses) {
        $db = Flight::db();

        $stmt = $db->prepare("INSERT INTO question (enonce) VALUES (?)");
        $stmt->execute([$enonce]);
        $idQuestion = $db->lastInsertId();

        $stmtReponse = $db->prepare("
            INSERT INTO reponse (id_question, texte, est_correcte) 
            VALUES (?, ?, ?)
        ");

        foreach ($reponses as $rep) {
            $stmtReponse->execute([
                $idQuestion,
                $rep['texte'],
                $rep['est_correcte'] ? 1 : 0
            ]);
        }

        return $idQuestion;
    }
}
