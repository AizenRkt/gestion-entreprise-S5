<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * LIGNES DE COMMANDE CLIENT
 * =============================================================================
 * 
 * RÔLE FONCTIONNEL:
 * -----------------
 * Stocke les détails des articles commandés par ligne.
 * Contient: article, quantité, prix unitaire, remise, TVA, montants.
 * 
 * RÈGLES MÉTIER REMISE (ÉTAPE 3):
 * -------------------------------
 * - Remise <= 10% : autorisée sans validation
 * - Remise > 10% et <= 30% : nécessite validation responsable
 * - Remise > 30% : INTERDITE
 */
class LigneCommandeClientModel
{
    /**
     * Récupère les lignes d'une commande
     */
    public function getByCommandeId(int $commandeId): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT lcd.*, a.designation, a.code AS article_code, a.unite
                                  FROM ligne_commande_client lcd
                                  LEFT JOIN article a ON lcd.id_article = a.id_article
                                  WHERE lcd.id_commande_client = :id
                                  ORDER BY lcd.id_ligne_commande_client ASC");
            $stmt->execute(['id' => $commandeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des lignes: ' . $e->getMessage());
        }
    }

    /**
     * =============================================================================
     * ÉTAPE 3 & 4 - RÈGLES MÉTIER REMISE
     * =============================================================================
     * 
     * CONDITION: Remise demandée
     * ACTION: Vérification du seuil et blocage si dépassement
     * ERREUR: "Remise non autorisée - validation requise" ou "Remise interdite"
     */
    public function create(array $data): int
    {
        $commandeId = (int)($data['id_commande_client'] ?? 0);
        $articleId = (int)($data['id_article'] ?? 0);
        $quantite = (float)($data['quantite'] ?? 0);
        $prixUnitaire = (float)($data['prix_unitaire'] ?? 0);
        $remisePourcent = (float)($data['remise_pourcent'] ?? 0);
        $tauxTva = (float)($data['taux_tva'] ?? 20);
        $remiseValidee = (bool)($data['remise_validee'] ?? false);

        // Validations de base
        if ($commandeId === 0 || $articleId === 0) {
            throw new Exception('Commande et article obligatoires.');
        }
        if ($quantite <= 0) {
            throw new Exception('La quantité doit être supérieure à 0.');
        }
        if ($prixUnitaire <= 0) {
            throw new Exception('Le prix unitaire doit être supérieur à 0.');
        }

        // =============================================================
        // BLOCAGE REMISE (ÉTAPE 4)
        // =============================================================
        if ($remisePourcent > CommandeClientModel::REMISE_MAX_ABSOLUE) {
            throw new Exception(
                "BLOCAGE: Remise de {$remisePourcent}% interdite. " .
                "Maximum autorisé: " . CommandeClientModel::REMISE_MAX_ABSOLUE . "%"
            );
        }

        if ($remisePourcent > CommandeClientModel::REMISE_MAX_SANS_VALIDATION && !$remiseValidee) {
            throw new Exception(
                "BLOCAGE: Remise de {$remisePourcent}% nécessite une validation. " .
                "Seuil sans validation: " . CommandeClientModel::REMISE_MAX_SANS_VALIDATION . "%"
            );
        }

        // Vérifier que la commande est en BROUILLON
        $commandeModel = new CommandeClientModel();
        $commande = $commandeModel->findById($commandeId);
        if (!$commande || $commande['statut'] !== CommandeClientModel::STATUT_BROUILLON) {
            throw new Exception('Impossible d\'ajouter une ligne: la commande n\'est pas en BROUILLON.');
        }

        // Calculs
        $montantBrut = $quantite * $prixUnitaire;
        $montantRemise = $montantBrut * ($remisePourcent / 100);
        $montantHt = $montantBrut - $montantRemise;
        $montantTva = $montantHt * ($tauxTva / 100);
        $montantTtc = $montantHt + $montantTva;

        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO ligne_commande_client 
                                  (id_commande_client, id_article, quantite, prix_unitaire, 
                                   remise_pourcent, remise_validee, taux_tva, 
                                   montant_ht, montant_tva, montant_ttc) 
                                  VALUES 
                                  (:commande, :article, :qte, :pu, :remise, :remise_valid, 
                                   :tva_rate, :ht, :tva, :ttc)");
            $stmt->execute([
                'commande' => $commandeId,
                'article' => $articleId,
                'qte' => $quantite,
                'pu' => $prixUnitaire,
                'remise' => $remisePourcent,
                'remise_valid' => $remiseValidee ? 1 : 0,
                'tva_rate' => $tauxTva,
                'ht' => $montantHt,
                'tva' => $montantTva,
                'ttc' => $montantTtc
            ]);

            $ligneId = (int)$db->lastInsertId();

            // Recalculer les totaux de la commande
            $this->recalculerTotauxCommande($commandeId);

            return $ligneId;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de l\'ajout de la ligne: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une ligne de commande
     */
    public function delete(int $ligneId): bool
    {
        try {
            $db = Flight::db();
            
            // Récupérer l'ID de la commande avant suppression
            $stmt = $db->prepare("SELECT id_commande_client FROM ligne_commande_client WHERE id_ligne_commande_client = :id");
            $stmt->execute(['id' => $ligneId]);
            $commandeId = (int)$stmt->fetchColumn();

            if ($commandeId === 0) {
                throw new Exception('Ligne introuvable.');
            }

            // Vérifier que la commande est en BROUILLON
            $commandeModel = new CommandeClientModel();
            $commande = $commandeModel->findById($commandeId);
            if (!$commande || $commande['statut'] !== CommandeClientModel::STATUT_BROUILLON) {
                throw new Exception('Impossible de supprimer: la commande n\'est pas en BROUILLON.');
            }

            $stmt = $db->prepare("DELETE FROM ligne_commande_client WHERE id_ligne_commande_client = :id");
            $result = $stmt->execute(['id' => $ligneId]);

            // Recalculer les totaux
            $this->recalculerTotauxCommande($commandeId);

            return $result;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Recalcule les totaux d'une commande
     */
    private function recalculerTotauxCommande(int $commandeId): void
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT 
                                    COALESCE(SUM(montant_ht), 0) AS total_ht,
                                    COALESCE(SUM(montant_tva), 0) AS total_tva,
                                    COALESCE(SUM(montant_ttc), 0) AS total_ttc
                                  FROM ligne_commande_client 
                                  WHERE id_commande_client = :id");
            $stmt->execute(['id' => $commandeId]);
            $totaux = $stmt->fetch(PDO::FETCH_ASSOC);

            $commandeModel = new CommandeClientModel();
            $commandeModel->updateMontants(
                $commandeId,
                (float)$totaux['total_ht'],
                (float)$totaux['total_tva'],
                (float)$totaux['total_ttc']
            );
        } catch (PDOException $e) {
            throw new Exception('Erreur lors du recalcul: ' . $e->getMessage());
        }
    }

    /**
     * Récupère la quantité restante à livrer pour une ligne
     */
    public function getQuantiteRestante(int $ligneId): float
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT 
                                    lcc.quantite AS quantite_commandee,
                                    COALESCE(SUM(llc.quantite_livree), 0) AS quantite_livree
                                  FROM ligne_commande_client lcc
                                  LEFT JOIN ligne_livraison_client llc ON lcc.id_ligne_commande_client = llc.id_ligne_commande_client
                                  WHERE lcc.id_ligne_commande_client = :id
                                  GROUP BY lcc.id_ligne_commande_client");
            $stmt->execute(['id' => $ligneId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return 0;
            }
            
            return max(0, (float)$result['quantite_commandee'] - (float)$result['quantite_livree']);
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }
}
