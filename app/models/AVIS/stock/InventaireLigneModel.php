<?php
namespace app\models\AVIS\stock;

use Flight;
use PDO;
use Exception;

class InventaireLigneModel {

    /**
     * Remplace toutes les lignes d’un inventaire
     * $lignes = [
     *   ['id_article'=>1, 'id_lot'=>null, 'qt_theorique'=>10, 'qt_physique'=>8, 'cout_unitaire'=>1200],
     * ]
     */
    public static function replaceLines(int $id_inventaire, array $lignes): bool {
        if ($id_inventaire <= 0) {
            throw new Exception('id_inventaire invalide');
        }

        $db = Flight::db();

        $del = $db->prepare("DELETE FROM inventaire_ligne WHERE id_inventaire = :i");
        $del->execute([':i' => $id_inventaire]);

        if (empty($lignes)) { return true; }

        $stmt = $db->prepare("
            INSERT INTO inventaire_ligne
            (id_inventaire, id_article, id_lot, quantite_theorique, quantite_physique, cout_unitaire)
            VALUES (:i, :a, :l, :qt, :qp, :c)
        ");

        foreach ($lignes as $l) {
            $stmt->execute([
                ':i'  => $id_inventaire,
                ':a'  => (int)$l['id_article'],
                ':l'  => $l['id_lot'] ?? null,
                ':qt' => (float)$l['qt_theorique'],
                ':qp' => (float)$l['qt_physique'],
                ':c'  => (float)$l['cout_unitaire']
            ]);
        }

        return true;
    }

    public static function getByInventaire(int $id_inventaire): array {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT il.*, 
                   a.code AS article_code,
                   a.designation,
                   l.lot_numero
            FROM inventaire_ligne il
            JOIN article a ON il.id_article = a.id_article
            LEFT JOIN lot l ON il.id_lot = l.id_lot
            WHERE il.id_inventaire = :i
            ORDER BY a.designation
        ");
        $stmt->execute([':i' => $id_inventaire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
