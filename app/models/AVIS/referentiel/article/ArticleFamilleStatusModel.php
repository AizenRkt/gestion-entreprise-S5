<?php
namespace app\models\AVIS\referentiel\article;
use Flight;
use PDO;
use Exception;

class ArticleFamilleStatusModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM article_famille_status ORDER BY id_article_famille_status DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_article_famille_status) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM article_famille_status WHERE id_article_famille_status = :id_article_famille_status");
            $stmt->execute([':id_article_famille_status' => $id_article_famille_status]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($libelle, $date_status = null) {
        try {
            $db = Flight::db();
            if ($date_status === null) {
                $date_status = date('Y-m-d H:i:s');
            }
            $stmt = $db->prepare("INSERT INTO article_famille_status (libelle, date_status) VALUES (:libelle, :date_status)");
            $stmt->execute([
                ':libelle' => $libelle,
                ':date_status' => $date_status
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_article_famille_status, $libelle, $date_status = null) {
        try {
            $db = Flight::db();
            if ($date_status === null) {
                $date_status = date('Y-m-d H:i:s');
            }
            $stmt = $db->prepare("UPDATE article_famille_status SET libelle = :libelle, date_status = :date_status WHERE id_article_famille_status = :id_article_famille_status");
            $stmt->execute([
                ':libelle' => $libelle,
                ':date_status' => $date_status,
                ':id_article_famille_status' => $id_article_famille_status
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_article_famille_status) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM article_famille_status WHERE id_article_famille_status = :id_article_famille_status");
            $stmt->execute([':id_article_famille_status' => $id_article_famille_status]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
