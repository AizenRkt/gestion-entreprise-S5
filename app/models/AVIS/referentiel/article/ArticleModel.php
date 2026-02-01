<?php
namespace app\models\AVIS\referentiel\article;
use Flight;
use PDO;
use Exception;

class ArticleModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT a.*, af.nom AS famille_nom, mv.code AS valorisation_libelle 
                FROM article a 
                LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille 
                LEFT JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation 
                ORDER BY a.id_article DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT a.*, af.nom AS famille_nom, af.code AS famille_code, mv.libelle AS valorisation_libelle 
                FROM article a 
                LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille 
                LEFT JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation 
                WHERE a.id_article = :id_article
            ");
            $stmt->execute([':id_article' => $id_article]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByCode($code) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT a.*, af.nom AS famille_nom, mv.libelle AS valorisation_libelle 
                FROM article a 
                LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille 
                LEFT JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation 
                WHERE a.code = :code
            ");
            $stmt->execute([':code' => $code]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($code, $designation, $id_famille_article_famille = null, $id_methode_valorisation = null, 
                          $unite = null, $prix_achat = null, $prix_vente = null, $stock_min = 0, $actif = true) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO article 
                (code, designation, id_famille_article_famille, id_methode_valorisation, unite, prix_achat, prix_vente, stock_min, actif) 
                VALUES 
                (:code, :designation, :id_famille, :id_methode, :unite, :prix_achat, :prix_vente, :stock_min, :actif)
            ");
            $stmt->execute([
                ':code' => $code,
                ':designation' => $designation,
                ':id_famille' => $id_famille_article_famille,
                ':id_methode' => $id_methode_valorisation,
                ':unite' => $unite,
                ':prix_achat' => $prix_achat,
                ':prix_vente' => $prix_vente,
                ':stock_min' => $stock_min,
                ':actif' => $actif ? 1 : 0
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_article, $code, $designation, $id_famille_article_famille = null, 
                                 $id_methode_valorisation = null, $unite = null, $prix_achat = null, 
                                 $prix_vente = null, $stock_min = 0, $actif = true) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE article 
                SET code = :code, designation = :designation, id_famille_article_famille = :id_famille, 
                    id_methode_valorisation = :id_methode, unite = :unite, prix_achat = :prix_achat, 
                    prix_vente = :prix_vente, stock_min = :stock_min, actif = :actif 
                WHERE id_article = :id_article
            ");
            $stmt->execute([
                ':code' => $code,
                ':designation' => $designation,
                ':id_famille' => $id_famille_article_famille,
                ':id_methode' => $id_methode_valorisation,
                ':unite' => $unite,
                ':prix_achat' => $prix_achat,
                ':prix_vente' => $prix_vente,
                ':stock_min' => $stock_min,
                ':actif' => $actif ? 1 : 0,
                ':id_article' => $id_article
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_article) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM article WHERE id_article = :id_article");
            $stmt->execute([':id_article' => $id_article]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function search($search_term) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT a.*, af.nom AS famille_nom, mv.libelle AS valorisation_libelle 
                FROM article a 
                LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille 
                LEFT JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation 
                WHERE a.code LIKE :search_term 
                OR a.designation LIKE :search_term
                ORDER BY a.designation
            ");
            $search_pattern = '%' . $search_term . '%';
            $stmt->execute([':search_term' => $search_pattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getByFamille($id_famille_article_famille) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT a.*, af.nom AS famille_nom, mv.libelle AS valorisation_libelle 
                FROM article a 
                LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille 
                LEFT JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation 
                WHERE a.id_famille_article_famille = :id_famille
                ORDER BY a.designation
            ");
            $stmt->execute([':id_famille' => $id_famille_article_famille]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getActive() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT a.*, af.nom AS famille_nom, mv.libelle AS valorisation_libelle 
                FROM article a 
                LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille 
                LEFT JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation 
                WHERE a.actif = 1
                ORDER BY a.designation
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
