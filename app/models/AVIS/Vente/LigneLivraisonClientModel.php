<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * LIGNES DE LIVRAISON CLIENT
 * 
 * Stocke les quantités réellement livrées par article.
 * Permet les livraisons partielles.
 */
class LigneLivraisonClientModel
{
    /**
     * Récupère les lignes d'une livraison
     */
    public function getByLivraisonId(int $livraisonId): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT llc.*, 
                                         a.designation, a.code AS article_code, a.unite,
                                         lcc.quantite AS quantite_commandee,
                                         lcc.prix_unitaire
                                  FROM ligne_livraison_client llc
                                  LEFT JOIN ligne_commande_client lcc ON llc.id_ligne_commande_client = lcc.id_ligne_commande_client
                                  LEFT JOIN article a ON lcc.id_article = a.id_article
                                  WHERE llc.id_livraison_client = :id
                                  ORDER BY llc.id_ligne_livraison_client ASC");
            $stmt->execute(['id' => $livraisonId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Ajoute une ligne de livraison
     * 
     * RÈGLES:
     * - La livraison doit être en BROUILLON
     * - La quantité ne peut pas dépasser le reste à livrer
     */
    public function create(array $data): int
    {
        $livraisonId = (int)($data['id_livraison_client'] ?? 0);
        $ligneCommandeId = (int)($data['id_ligne_commande_client'] ?? 0);
        $quantiteLivree = (float)($data['quantite_livree'] ?? 0);

        if ($livraisonId === 0 || $ligneCommandeId === 0) {
            throw new Exception('Livraison et ligne de commande obligatoires.');
        }
        if ($quantiteLivree <= 0) {
            throw new Exception('La quantité livrée doit être supérieure à 0.');
        }

        // Vérifier que la livraison est en BROUILLON
        $livraisonModel = new LivraisonClientModel();
        $livraison = $livraisonModel->findById($livraisonId);
        if (!$livraison || $livraison['statut'] !== LivraisonClientModel::STATUT_BROUILLON) {
            throw new Exception('Impossible d\'ajouter: la livraison n\'est pas en BROUILLON.');
        }

        // Vérifier la quantité restante à livrer
        $ligneCommandeModel = new LigneCommandeClientModel();
        $qteRestante = $ligneCommandeModel->getQuantiteRestante($ligneCommandeId);
        
        if ($quantiteLivree > $qteRestante) {
            throw new Exception("Quantité demandée ({$quantiteLivree}) supérieure au reste à livrer ({$qteRestante}).");
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO ligne_livraison_client 
                                  (id_livraison_client, id_ligne_commande_client, quantite_livree) 
                                  VALUES 
                                  (:livraison, :ligne_cmd, :qte)");
            $stmt->execute([
                'livraison' => $livraisonId,
                'ligne_cmd' => $ligneCommandeId,
                'qte' => $quantiteLivree
            ]);
            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une ligne de livraison
     */
    public function delete(int $ligneId): bool
    {
        try {
            $db = Flight::db();
            
            // Vérifier que la livraison est en BROUILLON
            $stmt = $db->prepare("SELECT lc.statut 
                                  FROM ligne_livraison_client llc
                                  JOIN livraison_client lc ON llc.id_livraison_client = lc.id_livraison_client
                                  WHERE llc.id_ligne_livraison_client = :id");
            $stmt->execute(['id' => $ligneId]);
            $statut = $stmt->fetchColumn();
            
            if ($statut !== LivraisonClientModel::STATUT_BROUILLON) {
                throw new Exception('Impossible de supprimer: la livraison n\'est pas en BROUILLON.');
            }

            $stmt = $db->prepare("DELETE FROM ligne_livraison_client WHERE id_ligne_livraison_client = :id");
            return $stmt->execute(['id' => $ligneId]);
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }
}
