<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class PaymentModeModel
{
    public function getAll(): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->query('SELECT id_mode_paiement, code, libelle FROM mode_paiement ORDER BY libelle ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les modes de paiement: ' . $e->getMessage());
        }
    }

    public function exists(int $modeId): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT 1 FROM mode_paiement WHERE id_mode_paiement = :id LIMIT 1');
            $stmt->execute(['id' => $modeId]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du mode de paiement: ' . $e->getMessage());
        }
    }
}
