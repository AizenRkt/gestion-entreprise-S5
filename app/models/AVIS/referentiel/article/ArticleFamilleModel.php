<?php
namespace app\models\AVIS\referentiel\article;
use Flight;
use PDO;
use Exception;

class ArticleFamilleModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM article_famille ORDER BY id_article_famille DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_article_famille) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM article_famille WHERE id_article_famille = :id_article_famille");
            $stmt->execute([':id_article_famille' => $id_article_famille]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByCode($code) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM article_famille WHERE code = :code");
            $stmt->execute([':code' => $code]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($code, $nom, $description = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO article_famille (code, nom, description) 
                VALUES (:code, :nom, :description)
            ");
            $stmt->execute([
                ':code' => $code,
                ':nom' => $nom,
                ':description' => $description
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_article_famille, $code, $nom, $description = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE article_famille 
                SET code = :code, nom = :nom, description = :description 
                WHERE id_article_famille = :id_article_famille
            ");
            $stmt->execute([
                ':code' => $code,
                ':nom' => $nom,
                ':description' => $description,
                ':id_article_famille' => $id_article_famille
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_article_famille) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM article_famille WHERE id_article_famille = :id_article_famille");
            $stmt->execute([':id_article_famille' => $id_article_famille]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function search($search_term) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT * FROM article_famille 
                WHERE code LIKE :search_term 
                OR nom LIKE :search_term 
                OR description LIKE :search_term
                ORDER BY nom
            ");
            $search_pattern = '%' . $search_term . '%';
            $stmt->execute([':search_term' => $search_pattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
