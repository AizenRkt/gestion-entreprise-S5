<?php
namespace app\models\AVIS\stock;

use Flight;
use PDO;
use Exception;

class MouvStockLotDetailModel {
    /**
     * Insère des détails de consommation de lots pour un mouvement de sortie.
     * $allocs: [ ['id_lot'=>int, 'quantite'=>float, 'cout_unitaire'=>float], ... ]
     */
    public static function replaceDetails(int $id_mouvement_stock, array $allocs): bool {
        if ($id_mouvement_stock <= 0) { throw new Exception('id_mouvement_stock invalide'); }
        $db = Flight::db();
        $del = $db->prepare("DELETE FROM mouvement_stock_lot_detail WHERE id_mouvement_stock = :m");
        $del->execute([':m' => $id_mouvement_stock]);
        if (empty($allocs)) { return true; }
        $stmt = $db->prepare("INSERT INTO mouvement_stock_lot_detail (id_mouvement_stock, id_lot, quantite, cout_unitaire, valeur) VALUES (:m, :l, :q, :c, :v)");
        foreach ($allocs as $al) {
            $q = (float)$al['quantite'];
            $c = (float)$al['cout_unitaire'];
            $v = round($q * $c, 2);
            $stmt->execute([':m' => $id_mouvement_stock, ':l' => (int)$al['id_lot'], ':q' => $q, ':c' => $c, ':v' => $v]);
        }
        return true;
    }

    public static function getByMovement(int $id_mouvement_stock): array {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT d.*, l.lot_numero, l.date_entree, l.date_limite_utilisation_optimale, l.date_limite_consommation FROM mouvement_stock_lot_detail d LEFT JOIN lot l ON d.id_lot = l.id_lot WHERE d.id_mouvement_stock = :m ORDER BY d.id_mouvement_stock_lot_detail");
        $stmt->execute([':m' => $id_mouvement_stock]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
