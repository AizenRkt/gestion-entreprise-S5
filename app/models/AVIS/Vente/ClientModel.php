<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 1 - ENTITÉ CLIENT
 * =============================================================================
 * 
 * RÔLE FONCTIONNEL:
 * -----------------
 * La table client stocke les informations des clients de l'entreprise.
 * Elle est le point d'entrée du flux de vente: sans client, pas de commande.
 * 
 * RELATIONS:
 * ----------
 * - client -> client_type (type de client: particulier, professionnel, etc.)
 * - client -> commande_client (un client peut avoir plusieurs commandes)
 * 
 * CHAMPS DE TRAÇABILITÉ:
 * ----------------------
 * - id_type: catégorisation du client
 */
class ClientModel
{
    /**
     * Récupère tous les clients avec leur type
     */
    public function getAll(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT c.*, ct.libelle AS type_libelle 
                    FROM client c 
                    LEFT JOIN client_type ct ON c.id_type = ct.id_client_type 
                    ORDER BY c.nom ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des clients: ' . $e->getMessage());
        }
    }

    /**
     * Récupère un client par son ID
     */
    public function findById(int $clientId): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT c.*, ct.libelle AS type_libelle 
                                  FROM client c 
                                  LEFT JOIN client_type ct ON c.id_type = ct.id_client_type 
                                  WHERE c.id_client = :id");
            $stmt->execute(['id' => $clientId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération du client: ' . $e->getMessage());
        }
    }

    /**
     * Crée un nouveau client
     */
    public function create(array $data): int
    {
        $nom = trim($data['nom'] ?? '');
        $telephone = trim($data['telephone'] ?? '');
        $email = trim($data['email'] ?? '');
        $adresse = trim($data['adresse'] ?? '');
        $idType = isset($data['id_type']) ? (int)$data['id_type'] : null;

        if ($nom === '') {
            throw new Exception('Le nom du client est obligatoire.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO client (nom, telephone, email, adresse, id_type) 
                                  VALUES (:nom, :telephone, :email, :adresse, :id_type)");
            $stmt->execute([
                'nom' => $nom,
                'telephone' => $telephone,
                'email' => $email,
                'adresse' => $adresse,
                'id_type' => $idType
            ]);
            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la création du client: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour un client
     */
    public function update(int $id, array $data): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE client SET 
                                  nom = :nom, 
                                  telephone = :telephone, 
                                  email = :email, 
                                  adresse = :adresse, 
                                  id_type = :id_type 
                                  WHERE id_client = :id");
            return $stmt->execute([
                'id' => $id,
                'nom' => trim($data['nom'] ?? ''),
                'telephone' => trim($data['telephone'] ?? ''),
                'email' => trim($data['email'] ?? ''),
                'adresse' => trim($data['adresse'] ?? ''),
                'id_type' => isset($data['id_type']) ? (int)$data['id_type'] : null
            ]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la mise à jour du client: ' . $e->getMessage());
        }
    }

    /**
     * Récupère tous les types de clients
     */
    public function getAllTypes(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM client_type ORDER BY libelle ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des types de clients: ' . $e->getMessage());
        }
    }

    /**
     * Alias pour getAllTypes
     */
    public function getTypes(): array
    {
        return $this->getAllTypes();
    }

    /**
     * Supprime un client
     */
    public function delete(int $id): bool
    {
        try {
            $db = Flight::db();
            // Vérifier si le client a des commandes
            $stmt = $db->prepare("SELECT COUNT(*) FROM commande_client WHERE id_client = :id");
            $stmt->execute(['id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Impossible de supprimer: le client a des commandes associées.');
            }
            
            $stmt = $db->prepare("DELETE FROM client WHERE id_client = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la suppression du client: ' . $e->getMessage());
        }
    }
}
