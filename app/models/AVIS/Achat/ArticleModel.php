<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class ArticleModel
{
    public function listWithStock(): array
    {
        try {
            $db = Flight::db();
            $sql = 'SELECT a.id_article, a.code, a.designation, a.prix_achat, a.unite, COALESCE(SUM(sc.quantite), 0) AS quantite_stock FROM article a LEFT JOIN stock_courant sc ON sc.id_article = a.id_article WHERE a.actif = 1 GROUP BY a.id_article, a.code, a.designation, a.prix_achat, a.unite ORDER BY a.designation ASC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les articles: ' . $e->getMessage());
        }
    }
}
