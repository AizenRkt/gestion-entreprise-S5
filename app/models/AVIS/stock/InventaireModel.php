<?php
namespace app\models\AVIS\stock;

use Flight;
use PDO;
use Exception;

class InventaireModel {

    public static function insert(array $data): int {
        $db = Flight::db();

        $stmt = $db->prepare("
            INSERT INTO inventaire
            (inventaire_numero, date_inventaire, id_depot, commentaire, cree_par)
            VALUES (:num, :date, :depot, :com, :user)
        ");

        $stmt->execute([
            ':num'   => $data['inventaire_numero'],
            ':date'  => $data['date_inventaire'],
            ':depot' => $data['id_depot'],
            ':com'   => $data['commentaire'] ?? null,
            ':user'  => $data['cree_par']
        ]);

        return (int)$db->lastInsertId();
    }

    public static function getById(int $id): array {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM inventaire WHERE id_inventaire = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function listAll(): array {
        $db = Flight::db();
        $stmt = $db->query("
            SELECT i.*, d.nom AS depot_nom
            FROM inventaire i
            LEFT JOIN depot d ON d.id_depot = i.id_depot
            ORDER BY i.date_inventaire DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateStatut(int $id, string $statut, int $user_id): bool {
        $db = Flight::db();

        $stmt = $db->prepare("
            UPDATE inventaire
            SET statut = :s,
                valide_par = :u,
                date_validation = NOW()
            WHERE id_inventaire = :id
        ");

        return $stmt->execute([
            ':s'  => $statut,
            ':u'  => $user_id,
            ':id' => $id
        ]);
    }

    /**
     * Valide l'inventaire et génère les mouvements d'ajustement
     */
    public static function valider(int $id, int $userId): void {
        $db = Flight::db();
        $db->beginTransaction();

        try {
            $inv = self::getById($id);
            if (!$inv) { throw new Exception("Inventaire introuvable"); }
            if ($inv['statut'] !== 'BROUILLON') { throw new Exception("Seul un inventaire en BROUILLON peut être validé"); }

            $lignes = InventaireLigneModel::getByInventaire($id);
            if (empty($lignes)) { throw new Exception("L'inventaire est vide"); }

            // Types de mouvements
            $stPlus = $db->prepare("SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code = 'INVENTAIRE_PLUS' LIMIT 1");
            $stPlus->execute();
            $idPlus = (int)$stPlus->fetchColumn();

            $stMoins = $db->prepare("SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code = 'INVENTAIRE_MOINS' LIMIT 1");
            $stMoins->execute();
            $idMoins = (int)$stMoins->fetchColumn();

            if (!$idPlus || !$idMoins) { throw new Exception("Types de mouvement INVENTAIRE_PLUS/MOINS non configurés"); }

            $mouvModel = new MouvStockModel();

            foreach ($lignes as $l) {
                $diff = (float)$l['quantite_physique'] - (float)$l['quantite_theorique'];
                if (abs($diff) < 0.0001) continue;

                $sens = ($diff > 0) ? 1 : 0;
                $type = ($diff > 0) ? $idPlus : $idMoins;
                $qtyAbs = abs($diff);

                $payload = [
                    'id_article' => $l['id_article'],
                    'id_depot'   => $inv['id_depot'],
                    'id_lot'     => $l['id_lot'],
                    'id_type_mouvement_stock' => $type,
                    'id_reference' => $id,
                    'table_reference' => 'inventaire',
                    'sens'       => $sens,
                    'quantite'   => $qtyAbs,
                    'cout_unitaire' => (float)$l['cout_unitaire'],
                    'motif'      => "Ajustement Inventaire " . $inv['inventaire_numero'],
                    'date_mouvement' => date('Y-m-d H:i:s'),
                    'created_by' => $userId
                ];

                $mouvId = $mouvModel->insert($payload);

                // Pour les sorties (écarts négatifs), on sélectionne manuellement le lot si c'est géré par lot
                if ($sens === 0 && !empty($l['id_lot'])) {
                    MouvStockLotDetailModel::replaceDetails($mouvId, [[
                        'id_lot' => $l['id_lot'],
                        'quantite' => $qtyAbs,
                        'cout_unitaire' => (float)$l['cout_unitaire'],
                        'valeur' => $qtyAbs * (float)$l['cout_unitaire']
                    ]]);
                }

                // Mise à jour stock courant
                StockCourantModel::upsert(
                    (int)$l['id_article'],
                    (int)$inv['id_depot'],
                    $diff, // delta positif ou négatif
                    (float)$l['cout_unitaire']
                );

                // Marquer le mouvement comme validé
                $db->prepare("UPDATE mouvement_stock SET date_validation = NOW() WHERE id_mouvement_stock = ?")->execute([$mouvId]);
                $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (?, 'validé', ?)")->execute([$mouvId, $userId]);
            }

            self::updateStatut($id, 'VALIDE', $userId);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
