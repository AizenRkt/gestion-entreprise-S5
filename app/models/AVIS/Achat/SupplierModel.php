<?php

namespace app\models\AVIS\Achat;

use Exception;
use Flight;
use PDO;
use PDOException;

class SupplierModel
{
    public function getAll(): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->query('SELECT id_fournisseur, nom, adresse, telephone, email FROM fournisseur ORDER BY nom ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Impossible de récupérer les fournisseurs: ' . $e->getMessage());
        }
    }

    public function exists(int $supplierId): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare('SELECT 1 FROM fournisseur WHERE id_fournisseur = :id LIMIT 1');
            $stmt->execute(['id' => $supplierId]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du fournisseur: ' . $e->getMessage());
        }
    }

    public function create(array $data): int
    {
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new Exception('Le nom du fournisseur est requis.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare('INSERT INTO fournisseur (nom, adresse, telephone, email) VALUES (:nom, :adresse, :telephone, :email)');
            $stmt->execute([
                'nom' => $name,
                'adresse' => $data['address'] ?? null,
                'telephone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            return (int) $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Impossible de créer le fournisseur: ' . $e->getMessage());
        }
    }
}
