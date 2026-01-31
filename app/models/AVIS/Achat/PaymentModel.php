<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class PaymentModel
{
    public function listPayments(): array
    {
        try {
            $db = Flight::db();
            $sql = 'SELECT p.id_paiement_fournisseur, p.paiement_numero, p.paiement_date, p.id_facture_fournisseur, p.montant_total, p.reference_paiement, p.cree_par, p.created_at, f.facture_numero FROM paiement_fournisseur p LEFT JOIN facture_fournisseur f ON p.id_facture_fournisseur = f.id_facture_fournisseur ORDER BY p.created_at DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les paiements: ' . $e->getMessage());
        }
    }

    public function create(array $data): int
    {
        $number = trim($data['paiement_numero'] ?? '');
        $date = $data['paiement_date'] ?? '';
        $invoiceId = (int) ($data['id_facture_fournisseur'] ?? 0);
        $amount = (float) ($data['montant_total'] ?? 0);
        $createdBy = (int) ($data['cree_par'] ?? 0);

        if ($number === '' || $invoiceId === 0 || $amount <= 0 || $createdBy === 0) {
            throw new Exception('Champs requis manquants pour le paiement.');
        }

        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de paiement invalide.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare('INSERT INTO paiement_fournisseur (paiement_numero, paiement_date, id_facture_fournisseur, montant_total, reference_paiement, cree_par) VALUES (:paiement_numero, :paiement_date, :id_facture_fournisseur, :montant_total, :reference_paiement, :cree_par)');
            $stmt->execute([
                'paiement_numero' => $number,
                'paiement_date' => date('Y-m-d H:i:s', $parsedDate),
                'id_facture_fournisseur' => $invoiceId,
                'montant_total' => $amount,
                'reference_paiement' => $data['reference_paiement'] ?? null,
                'cree_par' => $createdBy,
            ]);

            $paymentId = (int) $db->lastInsertId();

            $details = $data['details'] ?? [];
            if (is_array($details) && count($details) > 0) {
                $this->insertDetails($paymentId, $details);
            }

            return $paymentId;
        } catch (PDOException $e) {
            throw new Exception('Impossible de créer le paiement: ' . $e->getMessage());
        }
    }

    private function insertDetails(int $paymentId, array $details): void
    {
        $db = Flight::db();
        $stmt = $db->prepare('INSERT INTO paiement_fournisseur_detail (id_paiement_fournisseur, id_mode_paiement, montant) VALUES (:id_paiement_fournisseur, :id_mode_paiement, :montant)');

        foreach ($details as $detail) {
            $modeId = (int) ($detail['id_mode_paiement'] ?? 0);
            $amount = (float) ($detail['montant'] ?? 0);

            if ($modeId === 0 || $amount <= 0) {
                throw new Exception('Détail de paiement invalide.');
            }

            try {
                $stmt->execute([
                    'id_paiement_fournisseur' => $paymentId,
                    'id_mode_paiement' => $modeId,
                    'montant' => $amount,
                ]);
            } catch (PDOException $e) {
                throw new Exception('Erreur lors de l\'insertion du détail de paiement: ' . $e->getMessage());
            }
        }
    }
}
