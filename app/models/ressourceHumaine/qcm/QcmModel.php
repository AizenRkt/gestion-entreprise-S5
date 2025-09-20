<?php
namespace app\models\ressourceHumaine\qcm;
use Flight;
use PDO;
use Exception;

class QcmModel {

    public function insert($id_annonce, $titre, $note_max) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO qcm (id_annonce, titre, note_max) VALUES (:id_annonce, :titre, :note_max)");
            $stmt->execute([
                ':id_annonce' => $id_annonce,
                ':titre' => $titre,
                ':note_max' => $note_max
            ]);
            return "QCM inséré avec succès.";
        } catch (\PDOException $e) {
            return "Erreur d'insertion : " . $e->getMessage();
        }
    }

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM qcm ORDER BY date_creation DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_qcm) {
        try {
            $db = Flight::db();

            $sql = "
                SELECT q.id_question, q.enonce, r.id_reponse, r.texte AS texte_reponse, dq.bareme_question AS bareme
                FROM detail_qcm dq
                JOIN question q ON dq.id_question = q.id_question
                JOIN reponse r ON r.id_question = q.id_question
                WHERE dq.id_qcm = :id_qcm
                ORDER BY q.id_question;
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id_qcm' => $id_qcm]);

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $questions = [];
            foreach ($rows as $row) {
                $id_question = $row['id_question'];
                if (!isset($questions[$id_question])) {
                    $questions[$id_question] = [
                        'id_question' => $id_question,
                        'enonce' => $row['enonce'],
                        'bareme' => $row['bareme'],
                        'reponses' => []
                    ];
                }
                $questions[$id_question]['reponses'][] = [
                    'id_reponse' => $row['id_reponse'],
                    'texte' => $row['texte_reponse']
                ];
            }

            return array_values($questions);

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public static function delete($id_qcm) {
        try {
            $db = Flight::db();

            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM qcm WHERE id_qcm = :id_qcm");
            $stmt->execute([':id_qcm' => $id_qcm]);

            $db->commit();

            Flight::json([
                'success' => true,
                'message' => "Le QCM #$id_qcm a été supprimé avec succès."
            ]);

        } catch (\PDOException $e) {
            $db->rollBack();
            Flight::json([
                'success' => false,
                'message' => "Erreur lors de la suppression : " . $e->getMessage()
            ], 500);
        }
    }

    public static function create($id_profil, $titre, $note_max, $questions) {
        $db = Flight::db();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("INSERT INTO qcm (id_profil, titre, note_max) VALUES (?, ?, ?)");
            $stmt->execute([$id_profil, $titre, $note_max]);
            $id_qcm = $db->lastInsertId();

            $stmtDetail = $db->prepare("INSERT INTO detail_qcm (id_qcm, id_question, bareme_question) VALUES (?, ?, ?)");
            foreach ($questions as $q) {
                $stmtDetail->execute([$id_qcm, $q['id_question'], $q['bareme']]);
            }

            $db->commit();
            return $id_qcm; 

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }



}
