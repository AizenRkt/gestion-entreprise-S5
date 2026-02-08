<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class DepotModel
{
    public function getAll(): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->query('SELECT id_depot, code, nom, adresse FROM depot ORDER BY nom ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les dépôts: ' . $e->getMessage());
        }
    }

    public function exists(int $depotId): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT 1 FROM depot WHERE id_depot = :id LIMIT 1');
            $stmt->execute(['id' => $depotId]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du dépôt: ' . $e->getMessage());
        }
    }
}
