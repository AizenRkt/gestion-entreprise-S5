<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class PurchaseOrderModel
{
    public function listOrders(): array
    {
        try {
            $db = Flight::db();
            $sql = 'SELECT bc.id_bon_commande_fournisseur, bc.bc_numero, bc.bc_date, bc.id_fournisseur, bc.id_depot, bc.montant_ht, bc.montant_tva, bc.montant_ttc, bc.created_by, bc.created_at, f.nom AS supplier_name, d.nom AS depot_name FROM bon_commande_fournisseur bc LEFT JOIN fournisseur f ON bc.id_fournisseur = f.id_fournisseur LEFT JOIN depot d ON bc.id_depot = d.id_depot ORDER BY bc.created_at DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les bons de commande: ' . $e->getMessage());
        }
    }

    public function findById(int $orderId): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT * FROM bon_commande_fournisseur WHERE id_bon_commande_fournisseur = :id');
            $stmt->execute(['id' => $orderId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération du bon de commande: ' . $e->getMessage());
        }
    }

    public function existsByNumber(string $number): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT 1 FROM bon_commande_fournisseur WHERE bc_numero = :num LIMIT 1');
            $stmt->execute(['num' => $number]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du numéro BC: ' . $e->getMessage());
        }
    }

    public function create(array $data): int
    {
        $number = trim($data['bc_numero'] ?? '');
        $date = $data['bc_date'] ?? '';
        $supplierId = (int) ($data['id_fournisseur'] ?? 0);
        $depotId = (int) ($data['id_depot'] ?? 0);
        $createdBy = (int) ($data['created_by'] ?? 0);

        if ($number === '' || $supplierId === 0 || $depotId === 0 || $createdBy === 0) {
            throw new Exception('Champs requis manquants pour le bon de commande.');
        }

        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de BC invalide.');
        }

        if ($this->existsByNumber($number)) {
            throw new Exception('Le numéro de bon de commande existe déjà.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare('INSERT INTO bon_commande_fournisseur (bc_numero, bc_date, id_fournisseur, id_depot, montant_ht, montant_tva, montant_ttc, created_by) VALUES (:bc_numero, :bc_date, :id_fournisseur, :id_depot, :montant_ht, :montant_tva, :montant_ttc, :created_by)');
            $stmt->execute([
                'bc_numero' => $number,
                'bc_date' => date('Y-m-d H:i:s', $parsedDate),
                'id_fournisseur' => $supplierId,
                'id_depot' => $depotId,
                'montant_ht' => $data['montant_ht'] ?? 0,
                'montant_tva' => $data['montant_tva'] ?? 0,
                'montant_ttc' => $data['montant_ttc'] ?? 0,
                'created_by' => $createdBy,
            ]);

            return (int) $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Impossible de créer le bon de commande: ' . $e->getMessage());
        }
    }
}
