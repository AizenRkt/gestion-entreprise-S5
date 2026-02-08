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

    public function findById(int $id): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT r.*, bc.bc_numero, f.nom AS supplier_name, d.nom AS depot_name FROM reception_fournisseur r LEFT JOIN bon_commande_fournisseur bc ON r.id_bon_commande_fournisseur = bc.id_bon_commande_fournisseur LEFT JOIN fournisseur f ON r.id_fournisseur = f.id_fournisseur LEFT JOIN depot d ON r.id_depot = d.id_depot WHERE r.id_reception_fournisseur = :id');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération de la réception: ' . $e->getMessage());
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

        $db = Flight::db();

        try {
            $db->beginTransaction();

            // 1. Créer la réception
            $stmt = $db->prepare('INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by) VALUES (:reception_numero, :reception_date, :id_fournisseur, :id_bon_commande_fournisseur, :id_depot, :created_by)');
            $stmt->execute([
                'reception_numero' => $number,
                'reception_date' => date('Y-m-d H:i:s', $parsedDate),
                'id_fournisseur' => $supplierId,
                'id_bon_commande_fournisseur' => $orderId,
                'id_depot' => $depotId,
                'created_by' => $createdBy,
            ]);
            $receptionId = (int) $db->lastInsertId();

            // 2. Récupérer les lignes de la demande d'achat liée au BC
            $linesStmt = $db->prepare('
                SELECT dal.id_article, dal.quantite, dal.prix_unitaire
                FROM demande_achat_ligne dal
                JOIN demande_achat da ON dal.id_demande_achat = da.id_demande_achat
                JOIN bon_commande_fournisseur bc ON bc.id_demande_achat = da.id_demande_achat
                WHERE bc.id_bon_commande_fournisseur = :order_id
                AND dal.id_article IS NOT NULL
            ');
            $linesStmt->execute(['order_id' => $orderId]);
            $lines = $linesStmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Mettre à jour le stock pour chaque article
            foreach ($lines as $line) {
                $articleId = (int) $line['id_article'];
                $quantity = (float) $line['quantite'];
                $unitPrice = (float) $line['prix_unitaire'];

                if ($articleId > 0 && $quantity > 0) {
                    $this->updateStockOnReception($db, $articleId, $depotId, $quantity, $unitPrice, $receptionId, $createdBy, $parsedDate);
                }
            }

            $db->commit();
            return $receptionId;
        } catch (PDOException $e) {
            $db->rollBack();
            throw new Exception('Impossible de créer la réception: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour le stock lors d'une réception (entrée de stock)
     */
    private function updateStockOnReception(PDO $db, int $articleId, int $depotId, float $quantity, float $unitPrice, int $receptionId, int $createdBy, int $dateTimestamp): void
    {
        // Récupérer ou créer le type de mouvement "réception fournisseur"
        $typeId = $this->getOrCreateMovementType($db, 'RECEP_FOURN', 'Réception fournisseur', 'in');

        // Créer le mouvement de stock (entrée)
        $mvtStmt = $db->prepare('
            INSERT INTO mouvement_stock (id_article, id_depot, id_type_mouvement_stock, sens, quantite, cout_unitaire, motif, date_mouvement, date_validation, created_by)
            VALUES (:id_article, :id_depot, :id_type, 1, :quantite, :cout_unitaire, :motif, :date_mouvement, :date_validation, :created_by)
        ');
        $mvtStmt->execute([
            'id_article' => $articleId,
            'id_depot' => $depotId,
            'id_type' => $typeId,
            'quantite' => $quantity,
            'cout_unitaire' => $unitPrice,
            'motif' => 'Réception fournisseur #' . $receptionId,
            'date_mouvement' => date('Y-m-d H:i:s', $dateTimestamp),
            'date_validation' => date('Y-m-d H:i:s', $dateTimestamp),
            'created_by' => $createdBy,
        ]);

        // Mettre à jour stock_courant (INSERT ou UPDATE)
        $checkStmt = $db->prepare('SELECT quantite, valeur_stock FROM stock_courant WHERE id_article = :id_article AND id_depot = :id_depot');
        $checkStmt->execute(['id_article' => $articleId, 'id_depot' => $depotId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $addedValue = $quantity * $unitPrice;

        if ($existing) {
            // Mise à jour : ajouter la quantité et recalculer le coût moyen
            $newQty = (float) $existing['quantite'] + $quantity;
            $newValue = (float) $existing['valeur_stock'] + $addedValue;
            $newCoutMoyen = $newQty > 0 ? $newValue / $newQty : 0;

            $updateStmt = $db->prepare('
                UPDATE stock_courant 
                SET quantite = :quantite, valeur_stock = :valeur_stock, cout_moyen = :cout_moyen
                WHERE id_article = :id_article AND id_depot = :id_depot
            ');
            $updateStmt->execute([
                'quantite' => $newQty,
                'valeur_stock' => $newValue,
                'cout_moyen' => $newCoutMoyen,
                'id_article' => $articleId,
                'id_depot' => $depotId,
            ]);
        } else {
            // Insertion
            $insertStmt = $db->prepare('
                INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen)
                VALUES (:id_article, :id_depot, :quantite, :valeur_stock, :cout_moyen)
            ');
            $insertStmt->execute([
                'id_article' => $articleId,
                'id_depot' => $depotId,
                'quantite' => $quantity,
                'valeur_stock' => $addedValue,
                'cout_moyen' => $unitPrice,
            ]);
        }
    }

    /**
     * Récupère ou crée un type de mouvement de stock
     */
    private function getOrCreateMovementType(PDO $db, string $code, string $libelle, string $categoryCode): int
    {
        // Vérifier si le type existe
        $stmt = $db->prepare('SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code = :code');
        $stmt->execute(['code' => $code]);
        $typeId = $stmt->fetchColumn();

        if ($typeId) {
            return (int) $typeId;
        }

        // Récupérer la catégorie
        $catStmt = $db->prepare('SELECT id_categorie_mouvement_stock FROM mouvement_stock_categorie WHERE code = :code');
        $catStmt->execute(['code' => $categoryCode]);
        $catId = $catStmt->fetchColumn();

        if (!$catId) {
            throw new Exception("Catégorie de mouvement '$categoryCode' introuvable.");
        }

        // Créer le type
        $insertStmt = $db->prepare('INSERT INTO mouvement_stock_type (id_categorie_mouvement_stock, code, libelle) VALUES (:cat_id, :code, :libelle)');
        $insertStmt->execute([
            'cat_id' => $catId,
            'code' => $code,
            'libelle' => $libelle,
        ]);

        return (int) $db->lastInsertId();
    }
}
