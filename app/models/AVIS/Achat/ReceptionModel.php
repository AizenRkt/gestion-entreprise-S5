<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class ReceptionModel
{
    public function listReceptions(): array
    {
        try {
            $db = Flight::db();
            $sql = 'SELECT r.id_reception_fournisseur, r.reception_numero, r.reception_date, r.id_fournisseur, r.id_bon_commande_fournisseur, r.id_depot, r.created_by, r.created_at, bc.bc_numero, f.nom AS supplier_name, d.nom AS depot_name FROM reception_fournisseur r LEFT JOIN bon_commande_fournisseur bc ON r.id_bon_commande_fournisseur = bc.id_bon_commande_fournisseur LEFT JOIN fournisseur f ON r.id_fournisseur = f.id_fournisseur LEFT JOIN depot d ON r.id_depot = d.id_depot ORDER BY r.created_at DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les réceptions: ' . $e->getMessage());
        }
    }

    public function create(array $data): int
    {
        $number = trim($data['reception_numero'] ?? '');
        $date = $data['reception_date'] ?? '';
        $supplierId = (int) ($data['id_fournisseur'] ?? 0);
        $orderId = (int) ($data['id_bon_commande_fournisseur'] ?? 0);
        $depotId = (int) ($data['id_depot'] ?? 0);
        $createdBy = (int) ($data['created_by'] ?? 0);

        if ($number === '' || $supplierId === 0 || $orderId === 0 || $depotId === 0 || $createdBy === 0) {
            throw new Exception('Champs requis manquants pour la réception.');
        }

        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de réception invalide.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare('INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by) VALUES (:reception_numero, :reception_date, :id_fournisseur, :id_bon_commande_fournisseur, :id_depot, :created_by)');
            $stmt->execute([
                'reception_numero' => $number,
                'reception_date' => date('Y-m-d H:i:s', $parsedDate),
                'id_fournisseur' => $supplierId,
                'id_bon_commande_fournisseur' => $orderId,
                'id_depot' => $depotId,
                'created_by' => $createdBy,
            ]);

            return (int) $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Impossible de créer la réception: ' . $e->getMessage());
        }
    }
}
