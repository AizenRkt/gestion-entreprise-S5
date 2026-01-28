<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class PurchaseRequestModel
{
    private SupplierModel $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

    public function generateNumber(): string
    {
        $db = Flight::db();
        $lastNumber = $db->query('SELECT numero FROM demande_achat ORDER BY id_demande_achat DESC LIMIT 1')->fetchColumn();
        $counter = 1;

        if ($lastNumber && preg_match('/DMDA(\d+)/', $lastNumber, $matches)) {
            $counter = (int) $matches[1] + 1;
        }

        return sprintf('DMDA%05d', $counter);
    }

    public function create(array $payload): int
    {
        $requestDate = trim($payload['date'] ?? '');
        $supplierId = (int) ($payload['supplier_id'] ?? 0);
        $remark = trim($payload['remark'] ?? '');
        $createdBy = (int) ($payload['created_by'] ?? 0);
        $lines = $payload['lines'] ?? [];

        if ($requestDate === '' || $supplierId === 0 || $createdBy === 0) {
            throw new Exception('Date, fournisseur et utilisateur requis.');
        }

        if (!$this->supplierModel->exists($supplierId)) {
            throw new Exception('Fournisseur introuvable.');
        }

        if (empty($lines)) {
            throw new Exception('Au moins une ligne de produit est requise.');
        }

        $parsedDate = strtotime($requestDate);
        if ($parsedDate === false) {
            throw new Exception('Date de demande invalide.');
        }

        $totals = $this->computeTotals($lines);
        $db = Flight::db();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare('INSERT INTO demande_achat (numero, date_demande, id_fournisseur, remarque, statut, montant_ht, montant_tva, montant_ttc, created_by, created_at) VALUES (:numero, :date_demande, :id_fournisseur, :remarque, :statut, :montant_ht, :montant_tva, :montant_ttc, :created_by, NOW())');
            $stmt->execute([
                'numero' => $payload['numero'] ?? $this->generateNumber(),
                'date_demande' => date('Y-m-d', $parsedDate),
                'id_fournisseur' => $supplierId,
                'remarque' => $remark !== '' ? $remark : null,
                'statut' => 'CREE',
                'montant_ht' => $totals['ht'],
                'montant_tva' => $totals['tva'],
                'montant_ttc' => $totals['ttc'],
                'created_by' => $createdBy,
            ]);

            $requestId = (int) $db->lastInsertId();

            $lineStmt = $db->prepare('INSERT INTO demande_achat_ligne (id_demande_achat, id_article, code_article, designation, quantite, prix_unitaire, tva, quantite_stock) VALUES (:id_demande_achat, :id_article, :code_article, :designation, :quantite, :prix_unitaire, :tva, :quantite_stock)');

            foreach ($lines as $line) {
                $lineStmt->execute([
                    'id_demande_achat' => $requestId,
                    'id_article' => $line['article_id'] ?? null,
                    'code_article' => $line['product_code'] ?? null,
                    'designation' => $line['designation'],
                    'quantite' => $line['quantity'],
                    'prix_unitaire' => $line['unit_price'],
                    'tva' => $line['vat'],
                    'quantite_stock' => $line['stock_quantity'],
                ]);
            }

            $this->insertStatus($db, $requestId, 'CREE', $createdBy, null);

            $db->commit();
            return $requestId;
        } catch (PDOException $e) {
            $db->rollBack();
            throw new Exception('Impossible de créer la demande d\'achat: ' . $e->getMessage());
        }
    }

    public function listRequests(): array
    {
        try {
            $db = Flight::db();
            $sql = 'SELECT da.id_demande_achat, da.numero, da.date_demande, da.statut, da.remarque, da.montant_ttc, f.nom AS supplier_name FROM demande_achat da JOIN fournisseur f ON da.id_fournisseur = f.id_fournisseur ORDER BY da.date_demande DESC, da.id_demande_achat DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les demandes: ' . $e->getMessage());
        }
    }

    public function countRequests(): int
    {
        $db = Flight::db();
        $count = $db->query('SELECT COUNT(*) FROM demande_achat')->fetchColumn();
        return $count ? (int) $count : 0;
    }

    public function findWithLines(int $id): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT da.id_demande_achat, da.numero, da.date_demande, da.statut, da.remarque, da.montant_ht, da.montant_tva, da.montant_ttc, da.created_at, f.nom AS supplier_name, f.id_fournisseur FROM demande_achat da JOIN fournisseur f ON da.id_fournisseur = f.id_fournisseur WHERE da.id_demande_achat = :id');
            $stmt->execute(['id' => $id]);
            $header = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$header) {
                return null;
            }

            $lineStmt = $db->prepare('SELECT l.id_demande_achat_ligne, l.id_article, l.code_article, l.designation, l.quantite, l.prix_unitaire, l.tva, l.quantite_stock, a.code AS article_code_db FROM demande_achat_ligne l LEFT JOIN article a ON l.id_article = a.id_article WHERE l.id_demande_achat = :id ORDER BY l.id_demande_achat_ligne ASC');
            $lineStmt->execute(['id' => $id]);
            $lines = $lineStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $header['lines'] = $lines;
            return $header;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération de la demande: ' . $e->getMessage());
        }
    }

    public function updateStatus(int $id, string $status, int $userId, ?string $comment = null): void
    {
        $status = strtoupper($status);
        $allowed = ['CREE', 'VISEE', 'REJETEE'];
        if (!in_array($status, $allowed, true)) {
            throw new Exception('Statut invalide.');
        }

        $db = Flight::db();
        try {
            $db->beginTransaction();
            $update = $db->prepare('UPDATE demande_achat SET statut = :statut, updated_at = NOW() WHERE id_demande_achat = :id');
            $update->execute(['statut' => $status, 'id' => $id]);

            if ($update->rowCount() === 0) {
                throw new Exception('Demande introuvable.');
            }

            $this->insertStatus($db, $id, $status, $userId, $comment);
            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            throw new Exception('Impossible de mettre a jour le statut: ' . $e->getMessage());
        }
    }

    private function insertStatus(PDO $db, int $requestId, string $status, int $userId, ?string $comment): void
    {
        $stmt = $db->prepare('INSERT INTO demande_achat_statut (id_demande_achat, statut, commentaire, created_by) VALUES (:id_demande_achat, :statut, :commentaire, :created_by)');
        $stmt->execute([
            'id_demande_achat' => $requestId,
            'statut' => $status,
            'commentaire' => $comment,
            'created_by' => $userId,
        ]);
    }

    private function computeTotals(array $lines): array
    {
        $totals = ['ht' => 0.0, 'tva' => 0.0, 'ttc' => 0.0];

        foreach ($lines as $line) {
            $qty = (float) ($line['quantity'] ?? 0);
            $price = (float) ($line['unit_price'] ?? 0);
            $vatRate = (float) ($line['vat'] ?? 0);

            $lineHt = $qty * $price;
            $lineTva = $lineHt * ($vatRate / 100);

            $totals['ht'] += $lineHt;
            $totals['tva'] += $lineTva;
        }

        $totals['ttc'] = $totals['ht'] + $totals['tva'];
        return $totals;
    }
}
