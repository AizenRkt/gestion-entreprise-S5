<?php
namespace app\models\AVIS\stock;

use Flight;
use PDO;
use Exception;

class StockCourantModel {
    public static function getByArticleDepot(int $id_article, int $id_depot): ?array {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM stock_courant WHERE id_article = :id_article AND id_depot = :id_depot");
            $stmt->execute([':id_article' => $id_article, ':id_depot' => $id_depot]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) { return null; }
    }

    public static function upsert(int $id_article, int $id_depot, float $deltaQuantite, ?float $coutUnitaire = null): bool {
        try {
            $db = Flight::db();
            $existing = self::getByArticleDepot($id_article, $id_depot);
            if ($existing) {
                $newQte = (float)$existing['quantite'] + $deltaQuantite;
                $newValeur = (float)$existing['valeur_stock'];
                if ($coutUnitaire !== null) {
                    // valeur = ancienne valeur + delta * coût; pour sortie, delta est négatif
                    $newValeur = $newValeur + ($deltaQuantite * $coutUnitaire);
                }
                $newCump = null;
                if ($newQte > 0 && $newValeur >= 0) {
                    $newCump = round($newValeur / $newQte, 4);
                }
                $stmt = $db->prepare("UPDATE stock_courant SET quantite = :q, valeur_stock = :v, cout_moyen = :c WHERE id_article = :a AND id_depot = :d");
                return $stmt->execute([':q' => $newQte, ':v' => $newValeur, ':c' => $newCump, ':a' => $id_article, ':d' => $id_depot]);
            } else {
                $valeur = $coutUnitaire !== null ? ($deltaQuantite * $coutUnitaire) : 0;
                $cump = ($deltaQuantite > 0 && $coutUnitaire !== null) ? $coutUnitaire : null;
                $stmt = $db->prepare("INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen) VALUES (:a, :d, :q, :v, :c)");
                return $stmt->execute([':a' => $id_article, ':d' => $id_depot, ':q' => $deltaQuantite, ':v' => $valeur, ':c' => $cump]);
            }
        } catch (\PDOException $e) {
            throw new Exception("Erreur mise à jour stock courant: " . $e->getMessage());
        }
    }

    /**
     * Applique une entrée de stock: ajoute la quantité et la valeur (coût unitaire fourni)
     */
    public static function applyEntry(int $id_article, int $id_depot, float $quantite, float $coutUnitaire): bool {
        if ($quantite <= 0) { throw new Exception('Quantité entrée doit être > 0'); }
        return self::upsert($id_article, $id_depot, $quantite, $coutUnitaire);
    }

    /**
     * Applique une sortie avec CUMP: utilise le coût moyen courant, renvoie le coût unitaire utilisé.
     */
    public static function applyExitCUMP(int $id_article, int $id_depot, float $quantite): float {
        if ($quantite <= 0) { throw new Exception('Quantité sortie doit être > 0'); }
        $row = self::getByArticleDepot($id_article, $id_depot);
        $cump = isset($row['cout_moyen']) && $row['cout_moyen'] !== null ? (float)$row['cout_moyen'] : 0.0;
        if (!$row || (float)$row['quantite'] < $quantite) { throw new Exception('Stock insuffisant pour sortie'); }
        self::upsert($id_article, $id_depot, -$quantite, $cump);
        return $cump;
    }

    /**
     * Applique une sortie en valorisation par lots (FIFO/FEFO): soustrait par valeur fournie.
     * totalValeur = somme(q_i * coût_i). Quantité totale soustraite.
     */
    public static function applyExitByValue(int $id_article, int $id_depot, float $quantiteTotale, float $valeurTotale): bool {
        if ($quantiteTotale <= 0) { throw new Exception('Quantité sortie doit être > 0'); }
        $row = self::getByArticleDepot($id_article, $id_depot);
        if (!$row || (float)$row['quantite'] < $quantiteTotale) { throw new Exception('Stock insuffisant pour sortie'); }
        // Met à jour en deux temps pour préserver la valeur: upsert avec coût moyen effectif de la sortie
        $coutEffectif = $quantiteTotale > 0 ? ($valeurTotale / $quantiteTotale) : 0.0;
        return self::upsert($id_article, $id_depot, -$quantiteTotale, $coutEffectif);
    }
}

?>