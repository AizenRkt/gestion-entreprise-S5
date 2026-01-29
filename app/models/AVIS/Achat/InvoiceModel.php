<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class InvoiceModel
{
    public function listInvoices(): array
    {
        try {
            $db = Flight::db();
            $sql = 'SELECT f.id_facture_fournisseur, f.facture_numero, f.facture_date, f.id_fournisseur, f.id_reception_fournisseur, f.montant_ht, f.montant_tva, f.montant_ttc, f.created_by, f.created_at, r.reception_numero, s.nom AS supplier_name FROM facture_fournisseur f LEFT JOIN reception_fournisseur r ON f.id_reception_fournisseur = r.id_reception_fournisseur LEFT JOIN fournisseur s ON f.id_fournisseur = s.id_fournisseur ORDER BY f.created_at DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les factures: ' . $e->getMessage());
        }
    }

    public function create(array $data): int
    {
        $number = trim($data['facture_numero'] ?? '');
        $date = $data['facture_date'] ?? '';
        $supplierId = (int) ($data['id_fournisseur'] ?? 0);
        $receptionId = (int) ($data['id_reception_fournisseur'] ?? 0);
        $createdBy = (int) ($data['created_by'] ?? 0);

        if ($number === '' || $supplierId === 0 || $receptionId === 0 || $createdBy === 0) {
            throw new Exception('Champs requis manquants pour la facture.');
        }

        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de facture invalide.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare('INSERT INTO facture_fournisseur (facture_numero, facture_date, id_fournisseur, id_reception_fournisseur, montant_ht, montant_tva, montant_ttc, created_by) VALUES (:facture_numero, :facture_date, :id_fournisseur, :id_reception_fournisseur, :montant_ht, :montant_tva, :montant_ttc, :created_by)');
            $stmt->execute([
                'facture_numero' => $number,
                'facture_date' => date('Y-m-d H:i:s', $parsedDate),
                'id_fournisseur' => $supplierId,
                'id_reception_fournisseur' => $receptionId,
                'montant_ht' => $data['montant_ht'] ?? 0,
                'montant_tva' => $data['montant_tva'] ?? 0,
                'montant_ttc' => $data['montant_ttc'] ?? 0,
                'created_by' => $createdBy,
            ]);

            return (int) $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Impossible de créer la facture: ' . $e->getMessage());
        }
    }
}
