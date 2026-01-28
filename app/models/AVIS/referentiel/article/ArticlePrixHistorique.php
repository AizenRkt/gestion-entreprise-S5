<?php
namespace app\models\AVIS\referentiel\article;
use Flight;
use PDO;
use Exception;

class ArticlePrixHistoriqueModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT aph.*, a.code, a.designation 
                FROM article_prix_historique aph 
                JOIN article a ON aph.id_article = a.id_article 
                ORDER BY aph.id_article_prix_historique DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_article_prix_historique) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT aph.*, a.code, a.designation 
                FROM article_prix_historique aph 
                JOIN article a ON aph.id_article = a.id_article 
                WHERE aph.id_article_prix_historique = :id_article_prix_historique
            ");
            $stmt->execute([':id_article_prix_historique' => $id_article_prix_historique]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByArticle($id_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT aph.*, a.code, a.designation 
                FROM article_prix_historique aph 
                JOIN article a ON aph.id_article = a.id_article 
                WHERE aph.id_article = :id_article
                ORDER BY aph.date_modification DESC
            ");
            $stmt->execute([':id_article' => $id_article]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function insert($id_article, $prix_vente, $date_modification = null) {
        try {
            $db = Flight::db();
            if ($date_modification === null) {
                $date_modification = date('Y-m-d H:i:s');
            }
            $stmt = $db->prepare("
                INSERT INTO article_prix_historique (id_article, prix_vente, date_modification) 
                VALUES (:id_article, :prix_vente, :date_modification)
            ");
            $stmt->execute([
                ':id_article' => $id_article,
                ':prix_vente' => $prix_vente,
                ':date_modification' => $date_modification
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function delete($id_article_prix_historique) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM article_prix_historique WHERE id_article_prix_historique = :id_article_prix_historique");
            $stmt->execute([':id_article_prix_historique' => $id_article_prix_historique]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function getLastPriceByArticle($id_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT aph.* 
                FROM article_prix_historique aph 
                WHERE aph.id_article = :id_article
                ORDER BY aph.date_modification DESC
                LIMIT 1
            ");
            $stmt->execute([':id_article' => $id_article]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }
}
