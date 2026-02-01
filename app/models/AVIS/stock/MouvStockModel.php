<?php
namespace app\models\AVIS\stock;

use Flight;
use PDO;
use Exception;

class MouvStockModel {

    public static function getAll(array $filters = []): array {
        try {
            $db = Flight::db();
            $sql = "SELECT ms.*, a.designation AS article_designation, d.nom AS depot_nom, mst.libelle AS type_libelle
                    FROM mouvement_stock ms
                    LEFT JOIN article a ON ms.id_article = a.id_article
                    LEFT JOIN depot d ON ms.id_depot = d.id_depot
                    LEFT JOIN mouvement_stock_type mst ON ms.id_type_mouvement_stock = mst.id_type_mouvement_stock
                    ORDER BY ms.created_at DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function insert(array $data): int {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO mouvement_stock (
                id_article, id_depot, id_lot,
                id_type_mouvement_stock, id_reference, table_reference,
                sens, quantite, cout_unitaire, motif,
                date_mouvement, created_by
            ) VALUES (
                :id_article, :id_depot, :id_lot,
                :id_type_mouvement_stock, :id_reference, :table_reference,
                :sens, :quantite, :cout_unitaire, :motif,
                :date_mouvement, :created_by
            )");

            $stmt->execute([
                ':id_article' => $data['id_article'],
                ':id_depot' => $data['id_depot'],
                ':id_lot' => $data['id_lot'] ?? null,
                ':id_type_mouvement_stock' => $data['id_type_mouvement_stock'],
                ':id_reference' => $data['id_reference'] ?? 0,
                ':table_reference' => $data['table_reference'] ?? null,
                ':sens' => $data['sens'],
                ':quantite' => $data['quantite'],
                ':cout_unitaire' => $data['cout_unitaire'] ?? null,
                ':motif' => $data['motif'] ?? null,
                ':date_mouvement' => $data['date_mouvement'],
                ':created_by' => $data['created_by']
            ]);
            $id = (int) $db->lastInsertId();

            // Generate a unique movement number: MVT-YYYYMM-<id padded>
            try {
                $ym = date('Ym', strtotime($data['date_mouvement']));
                $numero = sprintf('MVT-%s-%06d', $ym, $id);
                $up = $db->prepare("UPDATE mouvement_stock SET mouvement_numero = :num WHERE id_mouvement_stock = :id");
                $up->execute([':num' => $numero, ':id' => $id]);
            } catch (\Exception $e) {
                // if number generation fails, do not block insertion; log silently
            }

            return $id;
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion mouvement : " . $e->getMessage());
        }
    }

    public static function validate(int $id_mouvement_stock, int $userId): bool {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            $up = $db->prepare("UPDATE mouvement_stock SET date_validation = NOW() WHERE id_mouvement_stock = :id");
            $up->execute([':id' => $id_mouvement_stock]);

            $st = $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (:id, 'validé', :user)");
            $st->execute([':id' => $id_mouvement_stock, ':user' => $userId]);

            $db->commit();
            return true;
        } catch (\PDOException $e) {
            try { Flight::db()->rollBack(); } catch (\Throwable $t) {}
            throw new Exception("Erreur validation mouvement : " . $e->getMessage());
        }
    }

    public static function getById(int $id_mouvement_stock): ?array {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT ms.*, a.designation AS article_designation, d.nom AS depot_nom, mst.libelle AS type_libelle
                                   FROM mouvement_stock ms
                                   LEFT JOIN article a ON ms.id_article = a.id_article
                                   LEFT JOIN depot d ON ms.id_depot = d.id_depot
                                   LEFT JOIN mouvement_stock_type mst ON ms.id_type_mouvement_stock = mst.id_type_mouvement_stock
                                   WHERE ms.id_mouvement_stock = :id");
            $stmt->execute([':id' => $id_mouvement_stock]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) { return null; }
    }

    public static function updateFields(int $id_mouvement_stock, array $fields): bool {
        if (empty($fields)) return true;
        $db = Flight::db();
        $set = [];
        $params = [':id' => $id_mouvement_stock];
        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[":$k"] = $v;
        }
        $sql = "UPDATE mouvement_stock SET " . implode(', ', $set) . " WHERE id_mouvement_stock = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}

?>