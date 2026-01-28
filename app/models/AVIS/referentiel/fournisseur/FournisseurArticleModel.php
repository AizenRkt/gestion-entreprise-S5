<?php
namespace app\models\AVIS\referentiel\fournisseur;
use Flight;
use PDO;
use Exception;

class FournisseurArticleModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT fa.*, f.nom AS fournisseur_nom, a.designation AS article_designation, a.code AS article_code 
                FROM fournisseur_article fa 
                JOIN fournisseur f ON fa.id_fournisseur = f.id_fournisseur 
                JOIN article a ON fa.id_article = a.id_article 
                ORDER BY fa.id_fournisseur_article DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_fournisseur_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT fa.*, f.nom AS fournisseur_nom, a.designation AS article_designation, a.code AS article_code 
                FROM fournisseur_article fa 
                JOIN fournisseur f ON fa.id_fournisseur = f.id_fournisseur 
                JOIN article a ON fa.id_article = a.id_article 
                WHERE fa.id_fournisseur_article = :id_fournisseur_article
            ");
            $stmt->execute([':id_fournisseur_article' => $id_fournisseur_article]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByFournisseur($id_fournisseur) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT fa.*, f.nom AS fournisseur_nom, a.designation AS article_designation, a.code AS article_code 
                FROM fournisseur_article fa 
                JOIN fournisseur f ON fa.id_fournisseur = f.id_fournisseur 
                JOIN article a ON fa.id_article = a.id_article 
                WHERE fa.id_fournisseur = :id_fournisseur
            ");
            $stmt->execute([':id_fournisseur' => $id_fournisseur]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getByArticle($id_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT fa.*, f.nom AS fournisseur_nom, a.designation AS article_designation 
                FROM fournisseur_article fa 
                JOIN fournisseur f ON fa.id_fournisseur = f.id_fournisseur 
                JOIN article a ON fa.id_article = a.id_article 
                WHERE fa.id_article = :id_article
            ");
            $stmt->execute([':id_article' => $id_article]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function insert($id_fournisseur, $id_article, $prix_achat = null, $delai_livraison = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO fournisseur_article (id_fournisseur, id_article, prix_achat, delai_livraison) 
                VALUES (:id_fournisseur, :id_article, :prix_achat, :delai_livraison)
            ");
            $stmt->execute([
                ':id_fournisseur' => $id_fournisseur,
                ':id_article' => $id_article,
                ':prix_achat' => $prix_achat,
                ':delai_livraison' => $delai_livraison
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_fournisseur_article, $prix_achat = null, $delai_livraison = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE fournisseur_article 
                SET prix_achat = :prix_achat, delai_livraison = :delai_livraison 
                WHERE id_fournisseur_article = :id_fournisseur_article
            ");
            $stmt->execute([
                ':prix_achat' => $prix_achat,
                ':delai_livraison' => $delai_livraison,
                ':id_fournisseur_article' => $id_fournisseur_article
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_fournisseur_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM fournisseur_article WHERE id_fournisseur_article = :id_fournisseur_article");
            $stmt->execute([':id_fournisseur_article' => $id_fournisseur_article]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
