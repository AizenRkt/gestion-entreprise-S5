<?php
namespace app\models\AVIS\stock;

use Flight;
use PDO;

class LotModel {
    public static function getByArticleDepot(int $id_article, int $id_depot): array {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM lot WHERE id_article = :id_article AND id_depot = :id_depot ORDER BY date_entree ASC");
            $stmt->execute([':id_article' => $id_article, ':id_depot' => $id_depot]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function insert(array $data): int {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO lot (
            id_article, id_depot, lot_numero, date_entree,
            quantite_initiale, cout_unitaire,
            date_limite_utilisation_optimale, date_limite_consommation
        ) VALUES (
            :id_article, :id_depot, :lot_numero, :date_entree,
            :quantite_initiale, :cout_unitaire,
            :date_limite_utilisation_optimale, :date_limite_consommation
        )");
        $stmt->execute([
            ':id_article' => $data['id_article'],
            ':id_depot' => $data['id_depot'],
            ':lot_numero' => $data['lot_numero'],
            ':date_entree' => $data['date_entree'],
            ':quantite_initiale' => $data['quantite_initiale'],
            ':cout_unitaire' => $data['cout_unitaire'],
            ':date_limite_utilisation_optimale' => $data['date_limite_utilisation_optimale'] ?? null,
            ':date_limite_consommation' => $data['date_limite_consommation'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Retourne les lots avec quantité restante, triés pour allocation.
     * $method: 'fifo' (date_entree ASC), 'lifo' (date_entree DESC) ou 'fefo' (date_limite_consommation/DUO ASC)
     * Exclut les lots expirés (DLC/DLUO passée).
     */
    public static function getAvailableForAllocation(int $id_article, int $id_depot, string $method = 'fifo', ?string $referenceDate = null): array {
        $db = Flight::db();
        $sql = "SELECT l.*, 
            (l.quantite_initiale - COALESCE(SUM(d.quantite),0)) AS quantite_restante
            FROM lot l
            LEFT JOIN mouvement_stock_lot_detail d ON d.id_lot = l.id_lot
            WHERE l.id_article = :a AND l.id_depot = :d
            GROUP BY l.id_lot";
        $stmt = $db->prepare($sql);
        $stmt->execute([':a' => $id_article, ':d' => $id_depot]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pivot = null;
        if ($referenceDate) {
            try {
                $pivot = new \DateTime($referenceDate);
            } catch (\Exception $e) {
                $pivot = null;
            }
        }
        if (!$pivot) {
            $pivot = new \DateTime('today');
        }
        // filtre expirés et > 0
        $rows = array_values(array_filter($rows, function($r) use ($pivot) {
            $rest = (float)($r['quantite_restante'] ?? 0);
            if ($rest <= 0) return false;
            $dlc = !empty($r['date_limite_consommation']) ? new \DateTime($r['date_limite_consommation']) : null;
            $dluo = !empty($r['date_limite_utilisation_optimale']) ? new \DateTime($r['date_limite_utilisation_optimale']) : null;
            // blocage si lot expiré
            if ($dlc) {
                $dlc->setTime(23, 59, 59);
                if ($pivot > $dlc) return false;
            }
            if ($dluo) {
                $dluo->setTime(23, 59, 59);
                if ($pivot > $dluo) return false;
            }
            return true;
        }));
        // tri
        usort($rows, function($a, $b) use ($method) {
            if ($method === 'fefo') {
                $aKey = !empty($a['date_limite_consommation']) ? $a['date_limite_consommation'] : ($a['date_limite_utilisation_optimale'] ?? $a['date_entree']);
                $bKey = !empty($b['date_limite_consommation']) ? $b['date_limite_consommation'] : ($b['date_limite_utilisation_optimale'] ?? $b['date_entree']);
                return strcmp((string)$aKey, (string)$bKey);
            } else if ($method === 'lifo') {
                return strcmp((string)$b['date_entree'], (string)$a['date_entree']);
            }
            return strcmp((string)$a['date_entree'], (string)$b['date_entree']);
        });
        return $rows;
    }

    /**
     * Alloue une quantité demandée sur les lots disponibles selon $method.
     * Retour: [ ['id_lot'=>..., 'quantite'=>..., 'cout_unitaire'=>...], ... ]
     */
    public static function allocateQuantity(int $id_article, int $id_depot, float $quantiteDemandee, string $method = 'fifo', ?string $referenceDate = null): array {
        if ($quantiteDemandee <= 0) { throw new \Exception('Quantité demandée doit être > 0'); }
        $lots = self::getAvailableForAllocation($id_article, $id_depot, $method, $referenceDate);
        $remaining = $quantiteDemandee;
        $allocs = [];
        foreach ($lots as $lot) {
            if ($remaining <= 0) break;
            $avail = (float)$lot['quantite_restante'];
            if ($avail <= 0) continue;
            $take = min($avail, $remaining);
            $allocs[] = [
                'id_lot' => (int)$lot['id_lot'],
                'quantite' => $take,
                'cout_unitaire' => (float)$lot['cout_unitaire']
            ];
            $remaining -= $take;
        }
        if ($remaining > 0) { throw new \Exception('Stock par lots insuffisant pour allocation'); }
        return $allocs;
    }
}

?>